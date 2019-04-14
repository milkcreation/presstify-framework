<?php

namespace tiFy\User\Role;

use tiFy\Contracts\User\RoleFactory as RoleFactoryContract;
use tiFy\Support\ParamsBag;

class RoleFactory extends ParamsBag implements RoleFactoryContract
{
    /**
     * Nom de qualification du rôle.
     * @var string
     */
    protected $name = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du role.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs)
    {
        $this->name = $name;

        $this->set($attrs)->parse();

        events()->trigger('user.role.factory.boot', [$this]);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
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