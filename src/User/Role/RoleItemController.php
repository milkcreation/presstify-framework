<?php

namespace tiFy\User\Role;

use tiFy\Contracts\User\UserRoleItemController;
use tiFy\Kernel\Parameters\AbstractParametersBag;

class RoleItemController extends AbstractParametersBag implements UserRoleItemController
{
    /**
     * Nom de qualification du rôle.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes  = [
        'display_name'  => '',
        'desc'          => '',
        'capabilities'  => []
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du role.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        parent::__construct($attrs);

        add_action(
            'init',
            function () {
                $name = $this->getName();

                if (!$role = get_role($name)) :
                    $role = add_role($name, $this->get('display_name'));
                endif;

                foreach ($this->get('capabilities', []) as $cap => $grant) :
                    if (!isset($role->capabilities[$cap]) || ($role->capabilities[$cap] !== $grant)) :
                        $role->add_cap($cap, $grant);
                    endif;
                endforeach;
            },
            1
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'display_name'  => $this->getName(),
            'desc'          => '',
            'capabilities'  => []
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $capabilities = [];
        foreach ($this->get('capabilities', []) as $cap => $grant) :
            if (is_numeric($cap)) :
                $cap = $grant;
                $grant = true;
            endif;
            $capabilities[$cap] = $grant;
        endforeach;
        $this->set('capabilities', $capabilities);
    }
}