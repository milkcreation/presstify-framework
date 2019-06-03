<?php declare(strict_types=1);

namespace tiFy\User\Role;

use tiFy\Contracts\User\RoleFactory as RoleFactoryContract;
use tiFy\Contracts\User\RoleManager as RoleManagerContract;

/**
 * Class Role
 * @package tiFy\User\Role
 *
 * @see https://codex.wordpress.org/Roles_and_Capabilities
 */
class RoleManager implements RoleManagerContract
{
    /**
     * Liste des éléments déclarés.
     * @var RoleFactoryContract[]
     */
    protected $items = [];

    /**
     * @inheritdoc
     */
    public function get(string $name): ?RoleFactoryContract
    {
        return $this->items[$name] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function register(string $name, array $attrs): RoleManagerContract
    {
        $this->set(new RoleFactory($name, $attrs));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function set(RoleFactoryContract $factory, ?string $name = null): RoleManagerContract
    {
        $this->items[$name ? : $factory->getName()] = $factory;

        return $this;
    }
}