<?php

namespace tiFy\Apps\Container;

use Illuminate\Support\Collection;
use tiFy\Apps\AppController;

class Container extends AppController implements ContainerInterface
{
    /**
     * Liste des services déclarés.
     * @var Service[]
     */
    protected $items = [];

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $providers = [];

    /**
     * Liste des alias de résolution de services.
     * @var array
     */
    protected $aliases = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        foreach ($this->providers as $provider) :
            $concrete = new $provider($this);

            if ($concrete instanceof ServiceProvider) :
                $this->appServiceProvider($concrete);
            endif;
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function bound($abstract)
    {
        return isset($this->items[$this->getAbstract($abstract)]);
    }

    /**
     * {@inheritdoc}
     */
    public function bind($abstract, $concrete = null, $singleton = false)
    {
        if (is_null($concrete)) :
            $concrete = $abstract;
        endif;

        if (!$alias = $this->getAlias($concrete)) :
            $alias = $abstract;
        endif;

        return $this->items[$abstract] = new Service($abstract, compact('alias', 'concrete', 'singleton'), $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias($concrete)
    {
        return array_search($concrete, $this->getAliases());
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * {@inheritdoc}
     */
    public function getAbstract($alias)
    {
        return (
            $exists = (new Collection($this->items))->first(function($item) use ($alias) {
                return $item->getAlias() === $alias;
            })
        )
            ? $exists->getAbstract()
            : $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getService($abstract)
    {
        $abstract = $this->getAbstract($abstract);
        if (isset($this->items[$abstract])) :
            return $this->items[$abstract];
        else :
            throw new \InvalidArgumentException(
                sprintf('(%s) n\'est pas distribué par le fournisseur de service.', $abstract),
                501
            );
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($abstract, $args = [])
    {
        try {
            $service = $this->getService($abstract);
        } catch (\InvalidArgumentException $e) {
            return \wp_die($e->getMessage(), '', 501);
        }

        return $service->build($args);
    }

    /**
     * {@inheritdoc}
     */
    public function singleton($abstract, $concrete = null)
    {
        return $this->bind($abstract, $concrete, true);
    }
}