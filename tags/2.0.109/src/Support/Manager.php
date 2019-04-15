<?php declare(strict_types=1);

namespace tiFy\Support;

use Psr\Container\ContainerInterface;
use tiFy\Contracts\Support\Manager as ManagerContract;

class Manager implements ManagerContract
{
    /**
     * Instance du conteneur d'injection de dépendances.
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Liste des éléments déclarés
     * @var array
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param ContainerInterface $container Conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->items[$key] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function register($key, array $attrs = [])
    {
        return $this->set([$key => $attrs]);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $item = null)
    {
        $keys = is_array($key) ? $key : [$key => $item];

        array_walk($keys, [$this, 'walk']);

        foreach ($keys as $k => $i) {
            $this->items[$k] = $i;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function walk(&$item, $key = null): void
    {

    }
}