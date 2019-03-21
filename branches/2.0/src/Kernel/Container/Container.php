<?php

namespace tiFy\Kernel\Container;

use Illuminate\Support\Collection;
use League\Container\Argument\RawArgument;
use League\Container\Container as LeagueContainer;
use League\Container\ReflectionContainer;
use League\Container\ServiceProvider\ServiceProviderInterface;
use tiFy\Contracts\Container\ContainerInterface;

class Container extends LeagueContainer implements ContainerInterface
{
    /**
     * Liste des services déclarés.
     * @var Service[]
     */
    protected static $items = [];

    /**
     * Liste des alias de résolution de services.
     * @var array
     */
    protected $aliases = [];

    /**
     * Activation de l'auto-wiring
     * @see http://container.thephpleague.com/2.x/auto-wiring/
     */
    protected $autoWiring = false;

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->autoWiring) :
            $this->delegate(new ReflectionContainer());
        endif;

        foreach ($this->getServiceProviders() as $serviceProvider) :
            $resolved = $this->singleton($serviceProvider)
                ->build([$this]);

            if ($resolved instanceof ServiceProviderInterface) :
                $this->addServiceProvider($resolved);
            endif;
        endforeach;

    }

    /**
     * {@inheritdoc}
     */
    public function addService($abstract, $attrs = [])
    {
        return new Service($abstract, $attrs, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function bound($abstract)
    {
        return isset(self::$items[$this->getAbstract($abstract)]) || $this->has($abstract);
    }

    /**
     * {@inheritdoc}
     */
    public function bind($abstract, $concrete = null, $singleton = false)
    {
        if (is_null($concrete)) :
            $concrete = $abstract;
        endif;

        $alias = $this->getAlias($abstract);

        return self::$items[$abstract] = $this->addService($abstract, compact('alias', 'concrete', 'singleton'));
    }

    /**
     * {@inheritdoc}
     */
    public function get($id, array $args = [])
    {
        return $this->resolve($id, $args) ? : parent::get($id, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias($abstract)
    {
        $alias = array_search($abstract, $this->getAliases());

        return $alias !== false ? $alias : $abstract;
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
        $items = self::$items;

        return (
            $exists = (new Collection($items))->first(function (Service $item) use ($alias) {
                return $item->getAlias() === $alias;
            })
        )
            ? $exists->getAbstract()
            : $alias;
    }

    /**
     * @inheritdoc
     */
    protected function getFromThisContainer($alias, array $args = [])
    {
        array_walk($args, function (&$arg) {
            $arg = new RawArgument($arg);
        });

        return parent::getFromThisContainer($alias, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getService($abstract)
    {
        $abstract = $this->getAbstract($abstract);

        if (isset(self::$items[$abstract])) :
            return self::$items[$abstract];
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
        return $this->serviceProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($alias, $args = [])
    {
        try {
            $resolved = $this->getService($alias);

            return $resolved->build($args);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setAlias($alias, $concrete)
    {
        $this->aliases[$alias] = $concrete;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function singleton($abstract, $concrete = null)
    {
        return $this->bind($abstract, $concrete, true);
    }
}