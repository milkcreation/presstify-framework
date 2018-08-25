<?php

namespace tiFy\User\SignIn;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use tiFy\App\AppInterface;
use tiFy\App\Item\AbstractAppItemController;
use tiFy\Components\Tools\Notices\NoticesTrait;

abstract class SignInHandleController extends AbstractAppItemController
{
    use NoticesTrait;

    /**
     * Classe de rappel de l'application associée.
     * @var AppInterface
     */
    protected $app;

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
     * @param AppInterface $app Classe de rappel de l'application associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], AppInterface $app)
    {
        $this->name = $name;

        parent::__construct($attrs, $app);
    }

    /**
     * Procédure de connection.
     *
     * @return void
     */
    private function _login()
    {
        check_admin_referer('tiFySignIn-in-' . $this->getName());

        $secure_cookie = '';

        if (($log = $this->app->appRequest('POST')->get('log', false)) && !force_ssl_admin()) :
            $user_name = \sanitize_user($log);
            if ($user = get_user_by('login', $user_name)) :
                if (get_user_option('use_ssl', $user->ID)) :
                    $secure_cookie = true;
                    force_ssl_admin(true);
                endif;
            endif;
        endif;

        $reauth = !$this->app->appRequest()->get('reauth') ? false : true;
        $user = \wp_signon([], $secure_cookie);

        if (!$this->app->appRequest('COOKIE')->get(LOGGED_IN_COOKIE)) :
            if (headers_sent()) :
                $user = new \WP_Error(
                    'test_cookie',
                    sprintf(
                        __('<strong>ERROR</strong>: Cookies are blocked due to unexpected output. For help, please see <a href="%1$s">this documentation</a> or try the <a href="%2$s">support forums</a>.'),
                        __('https://codex.wordpress.org/Cookies' ),
                        __('https://wordpress.org/support/')
                    )
                );
            elseif ($this->app->appRequest('POST')->get('testcookie') && !$this->app->appRequest('COOKIE')->get(TEST_COOKIE)) :
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
            $redirect_url = $this->app->appRequest()->get('redirect_to');

            if ($redirect_url = $this->getFormRedirect($redirect_url, $user)) :
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
        check_admin_referer('tiFySignIn-out-' . $this->getName());

        $user = wp_get_current_user();

        wp_logout();

        $redirect_url = $this->app->appRequest()->get('redirect_to');

        if ($redirect_url = $this->getLogoutRedirect($redirect_url, $user)) :
        else :
            $redirect_url = remove_query_arg(['action', '_wpnonce', 'tiFySignIn'], set_url_scheme('//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        endif;

        $redirect_url = add_query_arg('loggedout', true, $redirect_url);

        wp_safe_redirect($redirect_url);
        exit;
    }

    /**
     * Traitement des attributs de cartographie des messages d'erreurs.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    private function _parseErrorsMap($attrs = [])
    {
        $attrs = array_merge(
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

        return $attrs;
    }


    /**
     * Traitement des attributs de configuration du champ "Mot de passe".
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    private function _parseFormPassword($attrs = [])
    {
        $attrs = array_merge(
            [
                'title'    => __('Mot de passe', 'tify'),
                'attrs'    => [
                    'id'            => 'tiFySignIn-FormFieldPassword--' . $this->getName(),
                    'placeholder'   => __('Renseignez votre mot de passe', 'tify'),
                    'size'          => 20,
                    'autocomplete'  => 'current-password'
                ]
            ],
            $attrs
        );
        $attrs['name'] = 'pwd';

        if ($label = (string)Arr::get($attrs, 'label', '')) :
            $attrs['label'] = [];
            $attrs['label']['content'] = $label;
            $attrs['label']['attrs']['for'] = $attrs['attrs']['id'];
            $attrs['label']['attrs']['class'] = 'tiFySignIn-FormFieldLabel tiFySignIn-FormFieldLabel--password';
        endif;

        return $attrs;
    }

    /**
     * Traitement des attributs de configuration du champ "Se souvenir de moi".
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    private function _parseFormRemember($attrs = [])
    {
        $attrs = array_merge(
            [
                'label'         => __('Se souvenir de moi', 'tify'),
                'attrs'         => [
                    'id' => 'tiFySignIn-FormFieldRemember--' . $this->getName(),
                    'value'         => 0
                ]
            ],
            $attrs
        );
        $attrs['name'] = 'remember';

        if ($label = (string)Arr::get($attrs, 'label', '')) :
            $attrs['label'] = [];
            $attrs['label']['content'] = $label;
            $attrs['label']['attrs']['for'] = $attrs['attrs']['id'];
            $attrs['label']['attrs']['class'] = 'tiFySignIn-FormFieldLabel tiFySignIn-FormFieldLabel--remember';
        endif;

        return $attrs;
    }

    /**
     * Traitement des attributs de configuration du bouton de soumission.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    private function _parseFormSubmit($attrs = [])
    {
        $attrs = array_merge(
            [
                'attrs' => [
                    'id'    => 'tiFySignIn-FormFieldSubmit--' . $this->getName(),
                    'value' => __('Se connecter', 'tify')
                ]
            ],
            $attrs
        );
        $attrs['name'] = 'submit';

        return $attrs;
    }

    /**
     * Traitement des attributs de configuration du champ "Identifiant".
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    private function _parseFormUsername($attrs = [])
    {
        $attrs = array_merge(
            [
                'title'    => __('Identifiant', 'tify'),
                'attrs'    => [
                    'id'            => 'tiFySignIn-FormFieldUsername--' . $this->getName(),
                    'placeholder'   => __('Renseignez votre identifiant', 'tify'),
                    'size'          => 20,
                    'autocomplete'  => 'username'
                ]
            ],
            $attrs
        );
        $attrs['name'] = 'log';

        if ($label = (string)Arr::get($attrs, 'label', '')) :
            $attrs['label'] = [];
            $attrs['label']['content'] = $label;
            $attrs['label']['attrs']['for'] = $attrs['attrs']['id'];
            $attrs['label']['attrs']['class'] = 'tiFySignIn-FormFieldLabel tiFySignIn-FormFieldLabel--username';
        endif;

        return $attrs;
    }

    /**
     * Traitement des attributs de configuration du lien de déconnection.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    private function _parseLogoutLink($attrs = [])
    {
        $attrs = array_merge(
            [
                'redirect'   => '',
                'text'       => __('Déconnection', 'tify'),
                'class'      => 'tiFySignIn-logoutLink',
                'title'      => __('se déconnecter', 'tify'),
            ],
            $attrs
        );

        return $attrs;
    }

    /**
     * Traitement des attributs de lien vers l'interface de mot de passe oublié.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    private function _parseLostpasswordLink($attrs = [])
    {
        $attrs = array_merge(
            [
                'redirect' => '',
                'content'  => __('Mot de passe oublié', 'tify')
            ],
            $attrs
        );

        return $attrs;
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
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->appTemplates(
            [
                'directory'  => $this->app->appDirname() . '/templates',
                'controller' => SignInTemplateController::class
            ]
        );

        $macros = [
            'formAfter',
            'formAdditionnalFields',
            'formBefore',
            'formBody',
            'formErrors',
            'formFieldPassword',
            'formFieldUsername',
            'formFieldRemember',
            'formFieldSubmit',
            'formFooter',
            'formHeader',
            'formHiddenFields',
            'formInfos',
            'lostpasswordLink'
        ];
        foreach($macros as $macro) :
            $this->app->appTemplateMacro($macro, [$this, $macro]);
        endforeach;

        $this->app->appAddAction('wp_loaded', [$this, 'wp_loaded']);
    }

    /**
     * Affichage d'un gabarit
     *
     * @param string $template login|lostpassword_link|logout_link
     * @param array $attrs
     * @param bool $echo
     *
     * @return string
     */
    final public function display($template = 'form', $attrs = [], $echo = true)
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
     * Récupération de la liste des erreurs de soumission aux formulaires.
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
                $errors = [__('Erreur lors de la tentative d\'authentification', 'tify')];
                break;
            endif;
        endforeach;

        return $errors;
    }

    /**
     * Récupération de l'url de déconnection.
     *
     * @param string $redirect_url Url de redirection personnalisée.
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
        $args['tiFySignIn'] = $this->getName();

        $logout_url = add_query_arg($args, $this->get('redirect_url', site_url('wp-login.php', 'login')));
        $logout_url = wp_nonce_url($logout_url, 'tiFySignIn-out-' . $this->getName());

        return $logout_url;
    }

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    final public function getName()
    {
        return $this->name;
    }

    /**
     * Récupération de la liste des rôles autorisés à se connecter depuis l'interface de login.
     *
     * @return array
     */
    final public function getRoles()
    {
        return $this->get('roles', []);
    }

    /**
     * Vérifie si un gabarit est valide.
     *
     * @param string $template Identifiant de qualification (méthode) d'affichage de gabarit.
     *
     * @return bool
     */
    final public function isTemplate($template)
    {
        return in_array($template, ['form', 'lostpassword_link', 'logout_link']);
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('name', $this->getName());
        $this->set('form.attrs.name', "tiFySignIn-Form--{$this->getName()}");
        $this->set('form.attrs.method', 'post');
        $this->set('form.attrs.action', '');

        if(!$this->get('form.attrs.id')) :
            $this->set('form.attrs.id', 'tiFySignIn-form--' . $this->getName());
        endif;
        $this->set(
            'form.attrs.class',
            sprintf(
                $this->get('form.attrs.class', '%s'),
                'tiFySignIn-Form'
            )
        );

        if ($fields = $this->get('form.fields', ['username', 'password', 'remember', 'submit'])) :
            foreach ($fields as $field_name => $field_attrs) :
                if (is_numeric($field_name)) :
                    $field_name = $field_attrs;
                    $field_attrs = [];
                endif;

                if (!in_array($field_name, ['username', 'password', 'remember', 'submit'])) :
                    continue;
                endif;

                $this->set("form.fields.{$field_name}", call_user_func([$this, '_parseForm' . Str::studly($field_name)], $field_attrs));
            endforeach;
        endif;

        $this->set(
            'logout_link',
            $this->_parseLogoutLink($this->get('logout_link', []))
        );

        $this->set(
            'lost_password_link',
            $this->_parseLostpasswordLink($this->get('lost_password_link', []))
        );

        $this->set(
            'errors_map',
            $this->_parseErrorsMap($this->get('errors_map', []))
        );
    }

    /**
     * A l'issue du chargement complet de Wordpress.
     *
     * @return void
     */
    final public function wp_loaded()
    {
        // Bypass
        if (!$signin = $this->app->appRequest()->get('tiFySignIn', false)) :
            return;
        endif;

        if ($signin !== $this->getName()) :
            return;
        endif;

        $action = $this->app->appRequest()->get('action', 'login');
        switch ($action) :
            default :
                break;

            case 'login' :
                $this->app->appAddFilter('authenticate', [$this, 'authenticate'], 50, 3);
                $this->app->appAddAction('wp_login', [$this,'onLoginSuccess'], 10, 2);
                $this->_login();
                break;

            case 'logout' :
                $this->app->appAddAction('wp_logout', [$this, 'onLogoutSuccess']);
                $this->_logout();
                break;
        endswitch;
    }
}