<?php

namespace tiFy\Kernel\Container;

use Illuminate\Support\Arr;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ReflectionContainer;
use tiFy\Contracts\Container\ContainerInterface;
use tiFy\Contracts\Container\ServiceProviderInterface;

class ServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Classe de rappel du conteneur de services.
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [];

    /**
     * Liste des services à instance multiples auto-déclarés.
     * @var string[]
     */
    protected $bindings = [];

    /**
     * Liste des services à instance unique auto-déclarés.
     * @var string[]
     */
    protected $singletons = [];

    /**
     * Liste des alias de qualification de services.
     * @var array
     */
    protected $aliases = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->parse();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return parent::getContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function getSingletons()
    {
        return $this->singletons;
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleton($abstract)
    {
        $singletons = $this->getSingletons();

        return in_array($abstract, $singletons) || isset($singletons[$abstract]);
    }

    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        foreach($this->aliases as $alias => $concrete) :
            $this->getContainer()->setAlias($alias, $concrete);
        endforeach;

        $provides = [];
        if ($bindings = $this->getBindings()) :
            foreach ($bindings as $concrete) :
                $alias = $this->getContainer()->getAlias($concrete);
                $provides[$alias] = $concrete;
            endforeach;
        endif;

        if ($singletons = $this->getSingletons()) :
            foreach ($singletons as $concrete) :
                $alias = $this->getContainer()->getAlias($concrete);

                $provides[$alias] = $concrete;
            endforeach;
        endif;

        foreach($provides as $alias => $concrete) :
            array_push($this->provides, $concrete);

            if ($this->isSingleton($alias)) :
                $this->getContainer()->singleton($alias, $concrete);
            else :
                $this->getContainer()->bind($alias, $concrete);
            endif;
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {

    }
}