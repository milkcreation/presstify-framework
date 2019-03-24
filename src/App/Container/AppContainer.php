<?php

namespace tiFy\App\Container;

use BadMethodCallException;
use Exception;
use Illuminate\Support\Collection;
use League\Container\ServiceProvider\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use tiFy\tiFy;

/**
 * Class AbstractApp
 * @package tiFy\App
 *
 * @mixin tiFy
 */
class AppContainer implements ContainerInterface
{
    /**
     * Liste des services déclarés.
     * @var AppService[]
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
        foreach ($this->getServiceProviders() as $serviceProvider) :
            $resolved = $this->share($serviceProvider)->build();

            if ($resolved instanceof ServiceProviderInterface) :
                $resolved->setApp($this);
                $this->getContainer()->addServiceProvider($resolved);
            endif;
        endforeach;
    }

    /**
     * Délégation d'appel des méthodes du conteneur d'injection.
     *
     * @param string $name Nom de la méthode à appeler.
     * @param array $arguments Liste des variables passées en argument.
     *
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        try {
            return $this->getContainer()->$name(...$arguments);
        } catch (Exception $e) {
            throw new BadMethodCallException(sprintf(__('La méthode %s n\'est pas disponible.', 'tify'), $name));
        }
    }


    /**
     * @inheritdoc
     */
    public function addService($abstract, $attrs = [])
    {
        return new AppService($abstract, $attrs, $this);
    }

    /**
     * @inheritdoc
     */
    public function bound($abstract)
    {
        return isset(self::$items[$this->getAbstract($abstract)]) || $this->has($abstract);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function has($alias)
    {
        return $this->getContainer()->has($alias);
    }

    /**
     * @inheritdoc
     */
    public function get($id, array $args = [])
    {
        return $this->resolve($id, $args) ? : $this->getContainer()->get($id, $args);
    }

    /**
     * @inheritdoc
     */
    public function getAbstract($alias)
    {
        $items = self::$items;

        return (
        $exists = (new Collection($items))->first(function (AppService $item) use ($alias) {
            return $item->getAlias() === $alias;
        })
        )
            ? $exists->getAbstract()
            : $alias;
    }

    /**
     * @inheritdoc
     */
    public function getAlias($abstract)
    {
        $alias = array_search($abstract, $this->getAliases());

        return $alias !== false ? $alias : $abstract;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @inheritdoc
     */
    public function getContainer()
    {
        return tiFy::instance();
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getServiceProviders()
    {
        return config('app.providers', []);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function setAlias($alias, $concrete)
    {
        $this->aliases[$alias] = $concrete;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function singleton($abstract, $concrete = null)
    {
        return $this->bind($abstract, $concrete, true);
    }
}