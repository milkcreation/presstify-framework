<?php declare(strict_types=1);

namespace tiFy\User\Role;

use tiFy\Contracts\User\RoleFactory as RoleFactoryContract;
use tiFy\Contracts\User\RoleManager as RoleManagerContract;
use tiFy\Support\Manager;

/**
 * Class Role
 * @package tiFy\User\Role
 */
class RoleManager extends Manager implements RoleManagerContract
{
    /**
     * Liste des éléments déclarés.
     * @var RoleFactoryContract[]
     */
    protected $items = [];

    /**
     * {@inheritdoc}
     *
     * @return RoleFactoryContract
     */
    public function get($name): ?RoleFactoryContract
    {
        return parent::get($name);
    }

    /**
     * {@inheritdoc}
     *
     * @return RoleManagerContract
     */
    public function register($name, ...$args): RoleManagerContract
    {
        return $this->set([$name => $args[0] ?? []]);
    }

    /**
     * {@inheritdoc}
     *
     * @return RoleManagerContract
     */
    public function set($key, $value = null): RoleManagerContract
    {
        parent::set($key, $value);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function walk(&$item, $key = null): void
    {
        if (!$item instanceof RoleFactoryContract) {
            $name = $key;
            $attrs = $item;
            $item = $this->container->get('user.role.factory');
        } else {
            $name = null;
            $attrs = [];
        }
        /* @var RoleFactoryContract $item */
        $item->prepare($this, $name, $attrs);
    }
}