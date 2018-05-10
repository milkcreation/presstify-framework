<?php

namespace tiFy\User\Role;

use Illuminate\Support\Arr;
use tiFy\Ui\Ui;
use tiFy\Apps\AppController;

class RoleController extends AppController
{
    /**
     * Nom de qualification du rôle.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de configuration
     * @var array
     */
    protected $attributes  = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du role.
     * @param array $attrs {
     *      Liste des attributs de configuration.
     *
     *      @var string $display_name Nom d'affichage.
     *      @var string $desc Texte de description.
     *      @var array $capabilities {
     *          Liste des habilitations Tableau indexés des habilitations permises ou tableau dimensionné
     *
     *          @var string $cap Nom de l'habilitation => @var bool $grant privilege
     *      }
     * }
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        parent::__construct();

        // Définition de l'identifiant
        $this->name = $name;

        // Definition des attributs de configuration
        $this->attributes = $this->parse($attrs);

        // Définition des événements de déclenchement
        $this->appAddAction('init', 'init', 1);
        $this->appAddAction('tify_ui_register');
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        $role_name = $this->getName();

        // Création du rôle
        if (!$role = \get_role($role_name)) :
            $role = \add_role($role_name, $this->get('display_name'));
        endif;

        // Mise à jour des habilitations
        if ($capabilities = $this->get('capabilities')) :
            foreach ($capabilities as $cap => $grant) :
                if (!isset($role->capabilities[$cap]) || ($role->capabilities[$cap] !== $grant)) :
                    $role->add_cap($cap, $grant);
                endif;
            endforeach;
        endif;
    }

    /**
     * Déclaration d'interfaces utilisateur d'administration.
     *
     * @return void
     */
    public function tify_ui_register()
    {
        if (!$admin_ui = $this->get('admin_ui', false)) :
            return;
        endif;

        $admin_ui = $this->parseAdminUi($admin_ui);

        Ui::registerAdmin(
            'tiFyCoreRole-AdminUiUsers--' . $this->getName(),
            $admin_ui['global']
        );

        Ui::registerAdmin(
            'tiFyCoreRole-AdminUiUserList--' . $this->getName(),
            $admin_ui['list'],
            'admin'
        );
        Ui::registerAdmin(
            'tiFyCoreRole-AdminUiUserEdit--' . $this->getName(),
            $admin_ui['edit']
        );
    }

    /**
     * Récupération du nom de qualification du rôle.
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
     * @param array $attrs {
     *      Liste des attributs de configuration.
     *
     *      @var string $display_name Nom d'affichage.
     *      @var string $desc Texte de description.
     *      @var array $capabilities {
     *          Liste des habilitations Tableau indexés des habilitations permises ou tableau dimensionné
     *
     *          @var string $cap Nom de l'habilitation => @var bool $grant privilege
     *      }
     * }
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        $defaults = [
            'display_name'  => $this->getName(),
            'desc'          => '',
            'capabilities'  => []
        ];
        $attrs = \wp_parse_args($attrs, $defaults);

        // Traitement des habilitations
        if ($capabilities = $attrs['capabilities']) :
            $caps = [];
            foreach ($capabilities as $capability => $grant) :
                if (is_int($capability)) :
                    $capability = $grant;
                    $grant = true;
                endif;
                $caps[$capability] = $grant;
            endforeach;
            $attrs['capabilities'] = $caps;
        endif;

        return $attrs;
    }

    /**
     * Récupération de la liste de attributs de configuration.
     *
     * @return array
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de qualification de l'attributs
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = '')
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Traitement des attributs de configuration des interface utilisateurs d'administration.
     *
     *
     */
    final public function parseAdminUi($attrs = [])
    {
        $defaults = [
            'global'    => [
                'admin_menu'    => [
                    'menu_slug'     => 'tiFyCoreRole-AdminUiUsers--' . $this->getName(),
                    'menu_title'    => $this->getAttr('display_name'),
                    'position'      => 70
                ]
            ],
            'list'      =>  [
                'cb'            => 'UserListTable',
                'admin_menu'    => [
                    'menu_slug'     => 'tiFyCoreRole-AdminUiUsers--' . $this->getName(),
                    'parent_slug'   => 'tiFyCoreRole-AdminUiUsers--' . $this->getName(),
                    'menu_title'    => __('Tous les utilisateurs', 'tify'),
                    'position'      => 1
                ],
                'params'          => [
                    'roles'         => [$this->getName()]
                ],
                'handle'          => ['edit' => 'tiFyCoreRole-AdminUiUserEdit--' . $this->getName()]
            ],
            'edit'      =>  [
                'cb'            => 'UserEditForm',
                'admin_menu'    => [
                    'menu_slug'     => 'tiFyCoreRole-AdminUiUserEdit--' . $this->getName(),
                    'parent_slug'   => 'tiFyCoreRole-AdminUiUsers--' . $this->getName(),
                    'menu_title'    => __('Ajouter', 'tify'),
                    'position'      => 2
                ],
                'params'          => [
                    'roles'         => [$this->getName()]

                ],
                'handle'          => ['list' => 'tiFyCoreRole-AdminUiUserList--' . $this->getName()]
            ]
        ];

        if (is_bool($attrs)) :
            return $defaults;
        endif;

        foreach (['global', 'list', 'edit'] as $ui) :
            if (!isset($attrs[$ui])) :
                $attrs[$ui] = $defaults[$ui];
            else :
                if (isset($attrs[$ui]['admin_menu'])) :
                    $attrs[$ui]['admin_menu'] = array_merge(
                        $defaults[$ui]['admin_menu'],
                        $attrs[$ui]['admin_menu']
                    );
                endif;
                $attrs[$ui] = array_merge(
                    $attrs[$ui],
                    $defaults[$ui]
                );
            endif;
        endforeach;

        return $attrs;
    }
}