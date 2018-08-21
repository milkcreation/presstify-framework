<?php

namespace tiFy\App\Container;

use Illuminate\Support\Collection;
use tiFy\App;
use tiFy\Kernel\Container\ServiceInterface;
use tiFy\Kernel\Container\ServiceProviderInterface;

class Container extends App implements ContainerInterface
{
    /**
     * Liste des services déclarés.
     * @var ServiceInterface[]
     */
    protected $items = [];

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [];

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

        foreach ($this->getServiceProviders() as $serviceProvider) :
            $this->appContainer()->share($serviceProvider)->withArgument($this);
            $concrete = $this->appContainer()->get($serviceProvider);

            if ($concrete instanceof ServiceProviderInterface) :
                $this->appContainer()->addServiceProvider($concrete);
            endif;
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function addService($abstract, $attrs = [])
    {
        return new AppService($abstract, $attrs, $this);
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

        $alias = $this->getAlias($concrete);

        return $this->items[$abstract] = $this->addService($abstract, compact('alias', 'concrete', 'singleton'));
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias($concrete)
    {
        $alias = array_search($concrete, $this->getAliases());

        return $alias !== false ? $alias : $concrete;
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
    public function getServiceProviders()
    {
        return array_merge(
            $this->serviceProviders,
            $this->appConfig('providers', [])
        );
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