<?php

namespace tiFy\Components\Form\Addons\User;

use tiFy\Form\Fields\FieldItemController;
use tiFy\Form\Forms\FormHandleController;
use tiFy\Form\Forms\FormItemController;
use tiFy\Form\Addons\AbstractAddonController;

class User extends AbstractAddonController
{
    /**
     * Liste des options par défaut du formulaire associé.
     * @var array
     */
    protected $defaultFormOptions = [
        'roles' => [],
    ];

    /**
     * Liste des options par défaut des champs du formulaire associé.
     * @internal Liste des champs natifs : user_login (requis)|role|first_name|last_name|nickname|display_name|user_email (requis)|user_url|description|user_pass
     * @var array
     */
    protected $defaultFieldOptions = [
        'userdata' => false,
    ];

    /**
     * Liste des rôles concernés.
     * @var array
     */
    protected $roles = [];

    /**
     * Identifiant de l'utilisateur en relation avec le formulaire.
     * @var int
     */
    protected $userId = 0;

    /**
     * Indicateur d'édition de l'utilisateur en relation par l'utilisateur lui-même.
     * @var bool
     */
    protected $isProfile = true;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->callbacks = [
            'form_set_current'      => [$this, 'cb_form_set_current'],
            'handle_check_field'    => [$this, 'cb_handle_check_field'],
            'handle_submit_request' => [$this, 'cb_handle_submit_request'],
        ];
        $this->userId = get_current_user_id();
    }

    /**
     * Court-circuitage de la définition du formulaire courant.
     *
     * @param FormItemController $formController Classe de rappel du controleur de formulaire.
     *
     * @return void
     */
    public function cb_form_set_current($formController)
    {
        foreach ($formController->getFields() as $field) :
            // Bypass
            if (!$userdata = $this->getFieldOption($field, 'userdata', false)) :
                continue;
            endif;

            if ($userdata === 'user_pass') :
                $field->set('attrs.onpaste', 'off');
                $field->set('attrs.autocomplete', 'off');
            endif;
        endforeach;
    }

    /**
     * Vérification d'intégrité d'un champ.
     *
     * @param array $errors Liste des erreurs de traitement du formulaire.
     * @param FieldItemController $field Classe de rappel du champ à tester.
     *
     * @return void
     */
    public function cb_handle_check_field(&$errors, $field)
    {
        // Bypass
        if (!$userdata = $this->getFieldOption($field, 'userdata', false)) :
            return;
        endif;

        if (!$this->isNativeUserData($userdata)) :
            return;
        endif;

        if (!in_array($userdata, ['user_login', 'user_email', 'role'])) :
            return;
        endif;

        switch ($userdata) :
            // Identifiant de connexion
            case 'user_login' :
                if (!$this->isProfile() && get_user_by('login', $field->getValue())) :
                    $errors[] = __('Cet identifiant est déjà utilisé par un autre utilisateur', 'tify');
                endif;

                if (is_multisite()) :
                    // Lettres et/ou chiffres uniquement
                    $user_name = $field->getValue();
                    $orig_username = $user_name;
                    $user_name = preg_replace('/\s+/', '', sanitize_user($user_name, true));
                    if ($user_name != $orig_username || preg_match('/[^a-z0-9]/', $user_name)) :
                        $_errors[] = __(
                            'L\'identifiant de connexion ne devrait contenir que des lettres minuscules (a-z) et des chiffres',
                            'tify'
                        );
                    endif;

                    // Identifiant réservés
                    $illegal_names = get_site_option('illegal_names');
                    if (!is_array($illegal_names)) :
                        $illegal_names = ['www', 'web', 'root', 'admin', 'main', 'invite', 'administrator'];
                        add_site_option('illegal_names', $illegal_names);
                    endif;
                    if (in_array($user_name, $illegal_names)) :
                        $_errors[] = __('Désolé, cet identifiant de connexion n\'est pas permis', 'tify');
                    endif;

                    // Identifiant réservés personnalisés
                    $illegal_logins = (array)apply_filters('illegal_user_logins', []);
                    if (in_array(strtolower($user_name), array_map('strtolower', $illegal_logins))) :
                        $_errors[] = __('Désolé, cet identifiant de connexion n\'est pas permis', 'tify');
                    endif;

                    // Longueur minimale
                    if (strlen($user_name) < 4) :
                        $_errors[] = __('L\'identifiant de connexion doit contenir au moins 4 caractères', 'tify');
                    endif;

                    // Longueur maximale
                    if (strlen($user_name) > 60) :
                        $_errors[] = __(
                            'L\'identifiant de connexion ne doit pas contenir plus de 60 caractères',
                            'tify'
                        );
                    endif;

                    // Lettres obligatoire
                    if (preg_match('/^[0-9]*$/', $user_name)) :
                        $_errors[] = __('L\'identifiant de connexion doit aussi contenir des lettres', 'tify');
                    endif;
                endif;
                break;

            // Email
            case 'user_email' :
                if (!$this->isProfile() && get_user_by('email', $field->getValue())) :
                    $errors[] = __('Cet email est déjà utilisé par un autre utilisateur', 'tify');
                endif;
                break;

            // Role
            case 'role' :
                if (!$this->hasRole($field->getValue())) :
                    $_errors[] = __('L\'attribution de ce rôle n\'est pas autorisée.', 'tify');
                endif;
                break;
        endswitch;
    }

    /**
     * Court-circuitage du traitement de la requête du formulaire.
     *
     * @param FormHandleController $handleController Classe de rappel de traitement du formulaire.
     *
     * @return void
     */
    public function cb_handle_submit_request($handleController)
    {
        $request_data = [
            'user_login'           => '',
            'role'                 => '',
            'first_name'           => '',
            'last_name'            => '',
            'nickname'             => '',
            'display_name'         => '',
            'user_email'           => '',
            'user_url'             => '',
            'description'          => '',
            'user_pass'            => '',
            'show_admin_bar_front' => false,
        ];

        // Récupération des données utilisateurs dans les variables de requête
        foreach ($this->getFields() as $field) :
            if (!$userdata = $this->getFieldOption($field, 'userdata')) :
                continue;
            endif;

            if (!isset($request_data[$userdata])) :
                continue;
            endif;

            $request_data[$userdata] = $field->getValue(true);
        endforeach;

        // Traitement de l'identifiant et récupération des données utilisateur existante
        if (!$request_data['user_login'] && ($user = get_userdata($this->userId))) :
            foreach ($request_data as $data => $value) :
                if (in_array($data, ['user_pass'])) :
                    continue;
                endif;
                if (empty($value)) :
                    $request_data[$data] = $user->{$data};
                endif;
            endforeach;
        endif;

        // Traitement du rôle
        if (!$request_data['role']) :
            if (is_user_logged_in()) :
                $request_data['role'] = current(wp_get_current_user()->roles);
            elseif ($names = $this->getRoleNames()) :
                $request_data['role'] = current($names);
            else :
                $request_data['role'] = get_option('default_role', 'subscriber');
            endif;
        endif;

        // Traitement de l'affichage de la barre d'administration
        if ($this->hasRole($request_data['role'])) :
            $show_admin_bar_front =
                !$this->getRoleAttr(
                    $request_data['role'],
                    'show_admin_bar_front',
                    false
                )
                    ? 'false'
                    : '';
        endif;

        // Traitement de l'enregistrement de l'utilisateur
        // Mise à jour
        if ($current_user = get_userdata($this->getUserID())) :
            if (empty($request_data['user_pass'])) :
                unset($request_data['user_pass']);
            endif;
            if (empty($request_data['role'])) :
                unset($request_data['role']);
            endif;

            $exits_data = (array)get_userdata($current_user->ID)->data;
            unset($exits_data['user_pass']);
            $request_data = wp_parse_args($request_data, $exits_data);
            $user_id = wp_update_user($request_data);

        // Création
        else :
            if (is_multisite()) :
                $user_details = wpmu_validate_user_signup($request_data['user_login'], $request_data['user_email']);
                if (is_wp_error($user_details['errors']) && !empty($user_details['errors']->errors)) :
                    return $handleController->addError($user_details['errors']->get_error_message());
                endif;
            endif;

            $user_id = \wp_insert_user($request_data);
        endif;

        // Traitement des metadonnées et options utilisateur
        if (!is_wp_error($user_id)) :
            $this->setUserID($user_id);

            // Création ou modification des informations personnelles
            /** @var FieldItemController $field */
            foreach ($this->getFields() as $field) :
                if (!$userdata = $this->getFieldOption($field, 'userdata', false)) :
                    continue;
                endif;

                if ($userdata === 'meta') :
                    \update_user_meta($this->getUserID(), $field->getSlug(), $field->getValue(true));
                elseif ($userdata === 'option') :
                    \update_user_option($this->getUserID(), $field->getSlug(), $field->getValue(true));
                endif;
            endforeach;
        else :
            return $handleController->addError($user_id->get_error_message());
        endif;
    }

    /**
     * Initialisation de l'identifiant de l'utilisateur concerné par le formulaire.
     *
     * @param int $user_id Identifiant Wordpress de l'utilisateur.
     *
     * @return void
     */
    public function setUserID($user_id)
    {
        $this->userId = $user_id;
    }

    /**
     * Récupération de l'identifiant de l'utilisateur concerné par le formulaire.
     *
     * @return int
     */
    public function getUserID()
    {
        return $this->userId;
    }

    /**
     * Vérifie si le traitement du formulaire de l'utilisateur en relation avec le formualire est fait par l'utilisateur lui-même.
     *
     * @var bool
     */
    public function isProfile()
    {
        return $this->isProfile;
    }

    /**
     * Récupération de la liste des attributs de configuration des roles concernés.
     *
     * @return array
     */
    public function getRoles()
    {
        if ($this->roles) :
            return $this->roles;
        endif;

        $_roles = [];
        if ($roles = (array)$this->getFormOption('roles', [])) :
            foreach ($roles as $name => $attrs) :
                if (is_int($name) && is_string($attrs)) :
                    $name = $attrs;
                    $attrs = [];
                endif;

                $_roles[$name] = array_merge(
                    [
                        'capabilities'         => [],
                        'show_admin_bar_front' => false,
                    ],
                    $attrs
                );
            endforeach;
        endif;

        return $this->roles = $_roles;
    }

    /**
     * Récupération de la liste des noms de qualification des rôles concernés.
     *
     * @return string[]
     */
    public function getRoleNames()
    {
        if ($roles = $this->getRoles()) :
            return array_keys($roles);
        endif;
    }

    /**
     * Vérification de l'existance d'un rôle concerné.
     *
     * @param string $name Nom de qualification du rôle.
     *
     * @return bool
     */
    public function hasRole($name)
    {
        return array_key_exists($name, $this->getRoles());
    }

    /**
     * Récupération d'un attribut de configuration pour un rôle concerné.
     *
     * @param string $name Nom de qualification du rôle.
     * @param string $key Clé d'index de l'attribut à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getRoleAttr($name, $attr, $default = '')
    {
        $roles = $this->getRoles();
        if (isset($roles[$name][$attr])) :
            return $roles[$name][$attr];
        endif;

        return $default;
    }

    /**
     * Vérifie si un identifiant de donnée utilisateur correspond à une donnée native de Wordpress.
     *
     * @param string $userdata Identifiant de donné utilisateur.
     *
     * @return bool
     */
    public function isNativeUserData($userdata)
    {
        return in_array(
            $userdata,
            [
                'user_login',
                'role',
                'first_name',
                'last_name',
                'nickname',
                'display_name',
                'user_email',
                'user_url',
                'description',
                'user_pass',
            ]
        );
    }
}