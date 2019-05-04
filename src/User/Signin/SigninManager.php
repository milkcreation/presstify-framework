<?php declare(strict_types=1);

namespace tiFy\User\Signin;

use tiFy\Contracts\User\SigninFactory as SigninFactoryContract;
use tiFy\Contracts\User\SigninManager as SigninManagerContract;

class SigninManager implements SigninManagerContract
{
    /**
     * Liste des éléments déclarés.
     * @var SigninFactoryContract[]
     */
    protected $items = [];

    /**
     * @inheritdoc
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function get(string $name): ?SigninFactoryContract
    {
        return $this->items[$name] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function register(string $name, array $attrs): SigninManagerContract
    {
        $controller = $attrs['controller'] ?? null;

        /** @var SigninFactoryContract $factory */
        $factory = $controller ? new $controller($name, $attrs) : app()->get('user.signin.factory', [$name, $attrs]);

        $this->set($factory);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function set(SigninFactoryContract $factory, ?string $name = null): SigninManagerContract
    {
        $this->items[$name ? : $factory->getName()] = $factory;

        return $this;
    }
}