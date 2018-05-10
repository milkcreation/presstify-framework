<?php

namespace tiFy\User\Login;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use tiFy\Apps\AppController;
use tiFy\Field\Field;
use tiFy\Librairies\Notices\NoticesAwareTrait;
use tiFy\Partial\Partial;

class LoginController extends AppController
{
    use NoticesAwareTrait;

    /**
     * Nom de qualification du controleur.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du controleur.
     * @param array $attrs Lise des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        $this->parse($attrs);
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('wp_loaded');
    }

    /**
     * A l'issue du chargement complet de Wordpress.
     *
     * @return void
     */
    public function wp_loaded()
    {
        // Bypass
        if (!$tify_login = $this->appRequest('GET')->get('tiFyLogin', false)) :
            return;
        endif;

        if ($tify_login !== $this->getName()) :
            return;
        endif;

        $action = $this->appRequest('GET')->get('action', 'login');
        switch ($action) :
            default :
                break;

            case 'login' :
                $this->appAddFilter('authenticate', 'authenticate', 50, 3);
                $this->appAddAction('wp_login', 'onLoginSuccess', 10, 2);
                $this->_login();
                break;

            case 'logout' :
                $this->appAddAction('wp_logout', 'onLogoutSuccess');
                $this->_logout();
                break;
        endswitch;
    }
    
    /**
     * Procédure de connection.
     *
     * @return void
     */
    private function _login()
    {
        check_admin_referer('tiFyLogin-in-' . $this->getName());

        $secure_cookie = '';

        if (($log = $this->appRequest('POST')->get('log', false)) && !force_ssl_admin()) :
            $user_name = \sanitize_user($log);
            if ($user = get_user_by('login', $user_name)) :
                if (get_user_option('use_ssl', $user->ID)) :
                    $secure_cookie = true;
                    force_ssl_admin(true);
                endif;
            endif;
        endif;

        $reauth = !$this->appRequest()->get('reauth') ? false : true;
        $user = \wp_signon([], $secure_cookie);

        if (!$this->appRequest('COOKIE')->get(LOGGED_IN_COOKIE)) :
            if (headers_sent()) :
                $user = new \WP_Error(
                    'test_cookie',
                    sprintf(
                        __('<strong>ERROR</strong>: Cookies are blocked due to unexpected output. For help, please see <a href="%1$s">this documentation</a> or try the <a href="%2$s">support forums</a>.'),
                        __('https://codex.wordpress.org/Cookies' ),
                        __('https://wordpress.org/support/')
                    )
                );
            elseif ($this->appRequest('POST')->get('testcookie') && !$this->appRequest('COOKIE')->get(TEST_COOKIE)) :
                $user = new \WP_Error(
                    'test_cookie',
                    sprintf(
                        __('<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href="%s">enable cookies</a> to use WordPress.'),
                        __('https://codex.wordpress.org/Cookies')
                    )
                );
            endif;
        endif;

        if (!is_wp_error($user) && !$reauth) :
            $redirect_url = $this->appRequest()->get('redirect_to');

            if ($redirect_url = $this->getLoginFormRedirect($redirect_url, $user)) :
                if ($secure_cookie && false !== strpos($redirect_url, 'wp-admin')) :
                    $redirect_url = preg_replace('|^http://|', 'https://', $redirect_url);
                endif;
            else :
                $redirect_url = admin_url();
            endif;

            wp_safe_redirect($redirect_url);
            exit;
        else :
            $this->_setErrors($user);
        endif;
    }

    /**
     * Procédure de déconnection.
     *
     * @return void
     */
    private function _logout()
    {
        check_admin_referer('tiFyLogin-out-' . $this->getName());

        $user = wp_get_current_user();

        wp_logout();

        $redirect_url = $this->appRequest()->get('redirect_to');

        if ($redirect_url = $this->getLogoutRedirect($redirect_url, $user)) :
        else :
            $redirect_url = remove_query_arg(['action', '_wpnonce', 'tiFyLogin'], set_url_scheme('//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        endif;

        $redirect_url = add_query_arg('loggedout', true, $redirect_url);

        wp_safe_redirect($redirect_url);
        exit;
    }

    /**
     * Définition des erreurs de soumission aux formulaires.
     *
     * @param string|array|\WP_Error $errors Liste des erreurs.
     *
     * @return void
     */
    private function _setErrors($errors)
    {
        $errors_map = $this->get('errors_map', []);

        if (is_wp_error($errors)) :
            if ($errors->get_error_codes()) :
                foreach ($errors->get_error_codes() as $code) :
                    if (isset($errors_map[$code])) :
                        $this->noticesAdd('error', $errors_map[$code], ['alias' => $code]);
                        continue;
                    endif;

                    if(!$messages = $errors->get_error_messages()) :
                        $this->noticesAdd('error', $code, ['alias' => $code]);
                    endif;

                    foreach($messages as $message) :
                        $this->noticesAdd('error', $message, ['alias' => $code]);
                    endforeach;
                endforeach;
            else :
                $this->noticesAdd('error');
            endif;
        elseif(is_array($errors)) :
            foreach($errors as $code => $message) :
                if (isset($errors_map[$code])) :
                    $this->noticesAdd('error', $errors_map[$code], ['alias' => $code]);
                else :
                    $this->noticesAdd('error', $message, ['alias' => $code]);
                endif;
            endforeach;
        else :
            $this->noticesAdd('error', (string)$errors);
        endif;
    }

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    public function parse($attrs = [])
    {
        // Définition des attributs de configuration par défaut
        $defaults = [
            'login_form'    => [
                'id'       => 'tiFyLogin-Form--' . $this->getName(),
                'fields'   => ['username', 'password', 'remember', 'submit']
            ],
            'logout_link'   => [],
            'lost_password_link' => [],
            'roles'         => ['subscriber'],
            'redirect_url'  => site_url('/'),
            'attempt'       => -1,
            'errors_map'    => []
        ];
        $attrs = \wp_parse_args($attrs, $defaults);

        if (!empty($attrs['login_form'])) :
            if (!empty($attrs['login_form']['fields'])) :
                // Traitement des attributs de configuration des champs de formulaire
                $fields = [];
                foreach ($attrs['login_form']['fields'] as $field_name => $field_attrs) :
                    if (is_int($field_name )) :
                        $field_name = (string)$field_attrs;
                        $field_attrs = [];
                    endif;
                    if (!in_array($field_name, ['username', 'password', 'remember', 'submit'])) :
                        continue;
                    endif;
                    $fields[$field_name] = call_user_func([$this, 'parseLoginFormField' . Str::studly($field_name) . 'Attrs'], $field_attrs);
                endforeach;
                $attrs['login_form']['fields'] = $fields;
            endif;
        endif;

        // Traitement des attributs de configuration du lien de deconnexion
        $attrs['logout_link'] = $this->parseLogoutLinkAttrs($attrs['logout_link']);

        // Traitement des attributs de configuration de l'interface de mot de passe oublié
        $attrs['lost_password_link'] = $this->parseLostpasswordLinkAttrs($attrs['lost_password_link']);

        // Traitement des attributs de la cartographie des messages d'erreurs
        $attrs['errors_map'] = $this->parseErrorsMap($attrs['errors_map']);

        $this->attributes = $attrs;
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'index de l'attributs à récupérer.
     * @param mixed $defautl Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Récupération de la liste des attributs de configuration d'un champ du formulaire d'authentification
     *
     * @param string $field_name Identifiant de quaalification du champ
     *
     * @return array
     */
    final public function getFieldAttrs($field_name)
    {
        if (!in_array($field_name, ['username', 'password', 'remember', 'submit'])) :
            return [];
        endif;

        if (!$login_form = $this->get('login_form')) :
            return [];
        endif;

        if (!isset($login_form['fields'])) :
            return [];
        endif;

        $form_fields = $login_form['fields'];

        if (isset($form_fields[$field_name])) :
            return $form_fields[$field_name];
        endif;
    }

    /**
     * Récupération de la liste des erreurs de soumission aux formulaires
     *
     * @return array
     */
    final public function getErrors()
    {
        $errors = [];

        if (!$messages = $this->noticesGetMessages('error')) :
            return $errors;
        endif;

        foreach ($messages as $message) :
            if ($message) :
                $errors[] = $message;
            else :
                $errors = [];
                $errors[] = __('Erreur lors de la tentative d\'authentification', 'tify');
                break;
            endif;
        endforeach;

        return $errors;
    }

    /**
     * Récupération de la liste des rôles autorisés à se connecter depuis l'interface de login
     *
     * @return array
     */
    final public function getRoles()
    {
        return $this->get('roles', []);
    }

    /**
     * Vérifie si un gabarit est valide
     *
     * @param string $template Identifiant de qualification (méthode) d'affichage de gabarit
     *
     * @return bool
     */
    final public function isTemplate($template)
    {
        return in_array($template, ['login_form', 'lostpassword_link', 'logout_link']);
    }

    /**
     * Récupération de l'url de déconnection
     *
     * @param string $redirect_url Url de redirection personnalisée
     *
     * @return string
     */
    final public function getLogoutUrl($redirect_url = '')
    {
        $args = [];
        if($redirect_url) :
            $args['redirect_to'] = $redirect_url;
        endif;
        $args['action'] = 'logout';
        $args['tiFyLogin'] = $this->getName();

        $logout_url = add_query_arg($args, $this->get('redirect_url', site_url('wp-login.php', 'login')));
        $logout_url = wp_nonce_url($logout_url, 'tiFyLogin-out-' . $this->getName());

        return $logout_url;
    }

    /**
     * Affichage d'un gabarit
     *
     * @param string $template login_form|
     * @param array $attrs
     * @param bool $echo
     *
     * @return string
     */
    final public function display($template = 'login_form', $attrs = [], $echo = true)
    {
        if (!$this->isTemplate($template)) :
            return '';
        endif;

        $output = call_user_func([$this, Str::studly($template)], $attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }

    /**
     * SURCHAGE
     */
    /**
     * Vérification des droits d'authentification d'un utilisateur
     *
     * @param \WP_User $user
     * @param string $username Identifiant de l'utilisateur passé en argument de la requête d'authentification
     * @param string $password Mot de passe en clair passé en argument de la requête d'authentification
     *
     * @return \WP_Error|\WP_User
     */
    public function authenticate($user, $username, $password)
    {
        if (!is_wp_error($user) && ($roles = $this->getRoles()) && !array_intersect($user->roles, $this->getRoles())) :
            $user = new \WP_Error('role_not_allowed');
        endif;

        return $user;
    }

    /**
     * Action lancée en cas de succès de connection
     *
     * @param string  $user_login Identifiant de connection
     * @param \WP_User $user Object WP_User de l'utilisateur connecté
     *
     * @return void
     */
    public function onLoginSuccess($user_login, $user)
    {
        return;
    }

    /**
     * Action lancée en cas de succès de deconnection
     *
     * @return void
     */
    public function onLogoutSuccess()
    {
        return;
    }

    /**
     * Traitement des attributs de configuration du champ Identifiant
     *
     * @param array $attrs {
     *      Liste des attributs de configuration du champ Identifiant
     *      @see \tiFy\Field\Text\Text
     * }
     *
     * @return array
     */
    public function parseLoginFormFieldUsernameAttrs($attrs = [])
    {
        $defaults = [
            'label'         => __('Identifiant', 'tify'),
            'attrs'    => [
                'id'            => 'tiFyLogin-FormFieldUsername--' . $this->getName(),
                'placeholder'   => __('Renseignez votre identifiant', 'tify'),
                'size'          => 20
            ]
        ];
        $attrs = \wp_parse_args($attrs, $defaults);

        $attrs['name'] = 'log';

        return $attrs;
    }

    /**
     * Traitement des attributs de configuration du champ Mot de passe
     *
     * @param array $attrs {
     *      Liste des attributs de configuration du champ Mot de passe
     *      @see \tiFy\Field\Password\Password
     * }
     *
     * @return array
     */
    public function parseLoginFormFieldPasswordAttrs($attrs = [])
    {
        $defaults = [
            'label'             => __('Mot de passe', 'tify'),
            'attrs'    => [
                'id'            => 'tiFyLogin-FormFieldPassword--' . $this->getName(),
                'placeholder'   => __('Renseignez votre mot de passe', 'tify')
            ]
        ];
        $attrs = \wp_parse_args($attrs, $defaults);

        $attrs['name'] = 'pwd';

        return $attrs;
    }

    /**
     * Traitement des attributs de configuration du champ Se souvenir de moi
     *
     * @param array $attrs {
     *      Liste des attributs de configuration du champ Se souvenir de moi
     *      @see \tiFy\Field\Checkbox\Checkbox
     * }
     *
     * @return array
     */
    public function parseLoginFormFieldRememberAttrs($attrs = [])
    {
        $defaults = [
            'label'         => __('Se souvenir de moi', 'tify'),
            'attrs'         => [
                'id' => 'tiFyLogin-FormFieldRemember--' . $this->getName(),
                'value'         => 0
            ]
        ];
        $attrs = \wp_parse_args($attrs, $defaults);

        $attrs['name'] = 'remember';

        return $attrs;
    }

    /**
     * Traitement des attributs de configuration du champ bouton de soumission
     *
     * @param array $attrs {
     *      Liste des attributs de configuration du champ bouton de soumission
     *      @see \tiFy\Field\Submit\Submit
     * }
     *
     * @return array
     */
    public function parseLoginFormFieldSubmitAttrs($attrs = [])
    {
        $defaults = [
            'attrs' => [
                'id'    => 'tiFyLogin-FormFieldSubmit--' . $this->getName(),
                'value' => __('Se connecter', 'tify')
            ]
        ];
        $attrs = \wp_parse_args($attrs, $defaults);

        $attrs['name'] = 'submit';

        return $attrs;
    }

    /**
     * Traitement des attributs de configuration du lien de déconnection
     *
     * @param array $attrs {
     *      Liste des attributs de cartographie des messages d'erreurs
     * }
     *
     * @return array
     */
    public function parseLogoutLinkAttrs($attrs = [])
    {
        $defaults = [
            'redirect'   => '',
            'text'       => __('Déconnection', 'tify'),
            'class'      => 'tiFyLogin-LogoutLink',
            'title'      => __('se déconnecter', 'tify'),
        ];

        return \wp_parse_args($attrs, $defaults);
    }

    /**
     * Traitement des attributs de mot de passe oublié
     *
     * @param array $attrs {
     *      Liste des attributs de cartographie des messages d'erreurs
     * }
     *
     * @return array
     */
    public function parseLostpasswordLinkAttrs($attrs = [])
    {
        $defaults = [
            'redirect' => '',
            'text'     => __('Mot de passe oublié', 'tify')
        ];

        return \wp_parse_args($attrs, $defaults);
    }

    /**
     * Traitement des attributs de cartographie des messages d'erreurs
     *
     * @param array $attrs {
     *      Liste des attributs de cartographie des messages d'erreurs
     * }
     *
     * @return array
     */
    public function parseErrorsMap($attrs = [])
    {
        return array_merge(
            [
                'empty_username'        => __('le champ de l’identifiant est vide.', 'tify'),
                'empty_password'        => __('Veuillez renseigner le mot de passe.', 'tify'),
                'invalid_username'      => __('Nom d’utilisateur non valide.', 'tify'),
                'incorrect_password'    => __('Ce mot de passe ne correspond pas à l’identifiant fourni.', 'tify'),
                'authentication_failed' => __('Les idenfiants de connexion fournis sont invalides.', 'tify'),
                'role_not_allowed'      => __('Votre utilisateur n\'est pas autorisé à se connecter depuis cette interface.', 'tify')
            ],
            $attrs
        );
    }

    /**
     * Affichage du formulaire d'authentification
     *
     * @param array $attrs Attributs de configuration personnalisés
     *
     * @return string
     */
    public function loginForm()
    {
        $attrs = $this->get('login_form');
        $form_name = isset($attrs['name']) ? $attrs['name'] : "tiFyLogin-Form--" . $this->getName();
        $form_id = isset($attrs['id']) ? $attrs['id'] : "tiFyLogin-Form--" . $this->getName();
        $form_class = isset($attrs['class']) ? $attrs['class'] : "tiFyLogin-Form";

        $output  = "";

        // Pré-affichage du formulaire
        $output .= $this->loginFormBefore();

        // Ouverture du formulaire
        $output .= "<form name=\"{$form_name}\" id=\"{$form_id}\" class=\"{$form_class}\" action=\"\" method=\"post\">";

        // Champs cachés requis
        $output .= (string)Field::Hidden(
            [
                'name' => 'tiFyLogin',
                'value' => $this->getName()
            ]
        );
        $output .= (string)Field::Hidden(
            [
                'name' => '_wpnonce',
                'value' => \wp_create_nonce('tiFyLogin-in-' . $this->getName())
            ]
        );

        // Champs cachés
        $output .= $this->hiddenFields();
        
        // Entête du formulaire
        $output .= $this->loginFormHeader();
        
        // Corps du formulaire (champs de saisie)
        $output .= $this->loginFormFields();
        
        // Champs de formulaire additionnels
        $output .= $this->loginFormAdditionnalFields();
        
        // Mémorisation des informations de connection
        $output .= $this->loginFormFieldRemember();
        
        // Bouton de soumission filtré
        $output .= $this->login_form_field_submit();
        
        // Pied du formulaire
        $output .= $this->loginFormFooter();
        
        // Fermeture du formulaire
        $output .= "</form>";

        // post-affichage du formulaire
        $output .= $this->loginFormAfter();

        return $output;
    }

    /**
     * Pré-affichage du formulaire d'authentification
     *
     * @return string
     */
    public function loginFormBefore()
    {
        return  '';
    }

    /**
     * Post-affichage du formulaire d'authentification
     *
     * @return string
     */
    public function loginFormAfter()
    {
        return  '';
    }

    /**
     * Affichage des champs cachés (requis)
     *
     * @return string
     */
    public function hiddenFields()
    {
        return '';
    }

    /**
     * Affichage de l'entête du formulaire.
     *
     * @return string
     */
    public function loginFormHeader()
    {
        return $this->loginFormErrors();
    }

    /**
     * Affichage des message de notification d'informations.
     *
     * @return string
     */
    public function loginFormInfos()
    {
        if(!$infos = $this->noticesGetMessages('info')) :
            return '';
        endif;

        if (count($infos)>1) :
            $text = "<ol>";
            foreach ($infos as $message) :
                $text .= "<li>{$message}</li>";
            endforeach;
            $text .= "</ol>";
        else :
            $text = reset($infos);
        endif;

        return Partial::Notice(
            [
                'class' => "tiFyLogin-FormPart tiFyLogin-FormInfos",
                'text'  => $text,
                'type'  => 'info'
            ],
            false
        );
    }

    /**
     * Affichage des notification d'erreurs de soumission au formulaire d'authentification.
     *
     * @return string
     */
    public function loginFormErrors()
    {
        if(!$errors = $this->getErrors()) :
            return '';
        endif;

        if (count($errors)>1) :
            $text = "<ol>";
            foreach ($errors as $message) :
                $text .= "<li>{$message}</li>";
            endforeach;
            $text .= "</ol>";
        else :
            $text = reset($errors);
        endif;

        return Partial::Notice(
            [
                'class' => "tiFyLogin-FormPart tiFyLogin-FormErrors",
                'text'  => $text,
                'type'  => 'error'
            ],
            false
        );
    }

    /**
     * Affichage du corps du formulaire.
     *
     * @return string
     */
    public function loginFormFields()
    {
        $output = "";

        foreach (['username', 'password'] as $field_name) :
            $output .= call_user_func([$this, 'loginFormField' . Str::studly($field_name)]);
        endforeach;

        return $output;
    }

    /**
     * Affichage du champ "Identifiant" du formulaire d'authentification
     *
     * @return string
     */
    public function loginFormFieldUsername()
    {
        if(!$attrs = $this->getFieldAttrs('username', false)) :
            return '';
        endif;

        $output  = "";
        $output .= "<p class=\"tiFyLogin-Part tiFyLogin-FormFieldContainer tiFyLogin-FormFieldContainer--username\">";

        if ($attrs['label']) :
            $label = [];
            $label['content'] = $attrs['label'];
            $label['attrs'] = [];
            if (isset($attrs['attrs']['id'])) :
                $label['attrs']['for'] = $attrs['attrs']['id'];
            endif;
            $label['attrs']['class'] = 'tiFyLogin-FormFieldLabel tiFyLogin-FormFieldLabel--username';
            $output .= Field::Label($label);
        endif;

        $output .= Field::Text($attrs);
        $output .= "</p>";

        return $output;
    }

    /**
     * Affichage du champ "Mot de passe" du formulaire d'authentification
     *
     * @return string
     */
    public function loginFormFieldPassword()
    {
        if(!$attrs = $this->getFieldAttrs('password', false)) :
            return '';
        endif;

        $output  = "";
        $output .= "<p class=\"tiFyLogin-Part tiFyLogin-FormFieldContainer tiFyLogin-FormFieldContainer--password\">";

        if ($attrs['label']) :
            $label = [];
            $label['content'] = $attrs['label'];
            $label['attrs'] = [];
            if (isset($attrs['attrs']['id'])) :
                $label['attrs']['for'] = $attrs['attrs']['id'];
            endif;
            $label['attrs']['class'] = 'tiFyLogin-FormFieldLabel tiFyLogin-FormFieldLabel--password';
            $output .= Field::Label($label);
        endif;

        $output .= Field::Password($attrs);
        $output .= "</p>";

        return $output;
    }

    /**
     * Affichage du champ "Se souvenir de moi" du formulaire d'authentification
     *
     * @return string
     */
    public function loginFormFieldRemember()
    {
        if(!$attrs = $this->getFieldAttrs('remember', false)) :
            return '';
        endif;

        $output  = "";
        $output .= "<p class=\"tiFyLogin-Part tiFyLogin-FormFieldContainer tiFyLogin-FormFieldContainer--remember\">";

        $output .= Field::Checkbox($attrs);

        if ($attrs['label']) :
            $label = [];
            $label['content'] = $attrs['label'];
            $label['attrs'] = [];
            if (isset($attrs['attrs']['id'])) :
                $label['attrs']['for'] = $attrs['attrs']['id'];
            endif;
            $label['attrs']['class'] = 'tiFyLogin-FormFieldLabel tiFyLogin-FormFieldLabel--remember';
            $output .= Field::Label($label);
        endif;

        $output .= "</p>";

        return $output;
    }

    /**
     * Affichage du champ "Bouton de soumission" du formulaire d'authentification
     *
     * @return string
     */
    public function login_form_field_submit()
    {
        if(!$attrs = $this->getFieldAttrs('submit', false)) :
            return '';
        endif;

        $output  = "";
        $output .= "<p class=\"tiFyLogin-Part tiFyLogin-FormFieldContainer tiFyLogin-FormFieldContainer--submit\">";
        $output .= Field::Submit($attrs);
        $output .= "</p>";

        return $output;
    }

    /**
     * Affichage des champs additionnels du formulaire d'authentification
     *
     * @return string
     */
    public function loginFormAdditionnalFields()
    {
        return '';
    }

    /**
     * Affichage du pied de formulaire
     *
     * @return string
     */
    public function loginFormFooter()
    {
        return $this->lostpasswordLink();
    }

    /**
     * Affichage du lien vers l'interface de récupération de mot de passe oublié.
     *
     * @return string
     */
    public function lostpasswordLink()
    {
        $attrs = $this->get('lost_password_link');

        $output =   "<a href=\"" . \wp_lostpassword_url($attrs['redirect']) ."\"" .
                    " title=\"" . __( 'Récupération de mot de passe perdu', 'tify' ) . "\"" .
                    " class=\"tiFyLogin-LostPasswordLink\">" .
                        $attrs['text'] .
                    "</a>";

        return $output;
    }

    /**
     * Affichage du lien de déconnection.
     *
     * @param array $attrs Liste des attributs de personnalisation.
     *
     * @return string
     */
    public function logoutLink($attrs = [])
    {
        $attrs = array_merge(
            $this->get('logout_link', []),
            $custom_attrs
        );
        $url = $this->getLogoutUrl($attrs['redirect']);

        return "<a href=\"{$url}\" title=\"{$attrs['title']}\" class=\"{$attrs['class']}\">{$attrs['text']}</a>";
    }

    /**
     * Récupération de l'url de redirection du formulaire d'authentification
     *
     * @param string $redirect_url Url de redirection personnalisée
     * @param \WP_User $user Utilisateur courant
     *
     * @return string
     */
    public function getLoginFormRedirect($redirect_url = '', $user)
    {
        if (!$redirect_url) :
            $redirect_url = $this->get('redirect_url', admin_url());
        endif;

        return $redirect_url;
    }

    /**
     * Récupération de l'url de redirection du formulaire d'authentification
     *
     * @param string $redirect_url Url de redirection personnalisée
     * @param \WP_User $user Utilisateur courant
     *
     * @return string
     */
    public function getLogoutRedirect($redirect_url = '', $user)
    {
        return $redirect_url;
    }
}