<?php

/**
 * @see https://codex.wordpress.org/Roles_and_Capabilities
 */

namespace tiFy\User\Role;

use tiFy\Apps\AppController;
use tiFy\User\Role\RoleController;

class Role extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        $this->appAddAction('init', null, 0);
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        if ($config = $this->appConfig()) :
            foreach ($config as $name => $attrs) :
                $this->register($id, $attrs);
            endforeach;
        endif;

        do_action('tify_user_role_register', $this);
    }
    
    /**
     * Déclaration d'un rôle.
     *
     * @param string $name Nom de qualification du rôle.
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
     * @return RoleController
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.user.role.{$name}";
        if ($this->appServiceHas($alias)):
            return;
        endif;

        $this->appServiceShare($alias, new RoleController($id, $attrs));

        return $this->appServiceGet($alias);
    }

    /**
     * Récupération d'une classe de rappel d'un rôle déclaré.
     *
     * @param string $name Nom de qualification du rôle.
     *
     * @return null|RoleController
     */
    public function get($name)
    {
        $alias = "tfy.user.role.{$name}";
        if ($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;
    }
}