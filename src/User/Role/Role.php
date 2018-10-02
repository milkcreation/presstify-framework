<?php

/**
 * @see https://codex.wordpress.org/Roles_and_Capabilities
 */

namespace tiFy\User\Role;

use tiFy\Contracts\User\UserRoleItemController;
use tiFy\User\Role\RoleController;

class Role
{
    /**
     * Liste des éléments déclarés.
     * @var UserRoleItemController[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'init',
            function () {
                foreach (config('user.role') as $name => $attrs) :
                    $this->_register($name, $attrs);
                endforeach;
            },
            0
        );
    }

    /**
     * Déclaration de rôle.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return $this
     */
    public function add($name, $attrs = [])
    {
        config()->set("user.role.{$name}", $attrs);

        return $this;
    }

    /**
     * Enregistrement d'un rôle déclaré.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return UserRoleItemController
     */
    private function _register($name, $attrs = [])
    {
        if (isset($this->items[$name])) :
            return $this->items[$name];
        endif;

        return $this->items[$name] = new RoleItemController($name, $attrs);
    }

    /**
     * Récupération d'une instance de rôle déclaré.
     *
     * @param string $name Nom de qualification du rôle.
     *
     * @return null|UserRoleItemController
     */
    public function get($name)
    {
        return isset($this->items[$name])
            ? $this->items[$name]
            : null;
    }
}