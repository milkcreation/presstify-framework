<?php

namespace tiFy\Kernel\Container;

use Illuminate\Support\Arr;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ReflectionContainer;
use tiFy\Kernel\Container\Container;

class ServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Classe de rappel du conteneur de services.
     * @var Container
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
     * @var array|string[]
     */
    protected $bindings = [];

    /**
     * Liste des services à instance unique auto-déclarés.
     * @var array|string[]
     */
    protected $singletons = [];

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
        $provides = [];
        if ($bindings = $this->getBindings()) :
            foreach ($bindings as $abstract => $concrete) :
                if (is_numeric($abstract)) :
                    $abstract = $concrete;
                endif;
                $provides[$abstract] = $concrete;
            endforeach;
        endif;

        if ($singletons = $this->getSingletons()) :
            foreach ($singletons as $abstract => $concrete) :
                if (is_numeric($abstract)) :
                    $abstract = $concrete;
                endif;
                $provides[$abstract] = $concrete;
            endforeach;
        endif;

        foreach($provides as $abstract => $concrete) :
            array_push($this->provides, $abstract);

            if ($this->isSingleton($abstract)) :
                $this->getContainer()->singleton($abstract, $concrete);
            else :
                $this->getContainer()->bind($abstract, $concrete);
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