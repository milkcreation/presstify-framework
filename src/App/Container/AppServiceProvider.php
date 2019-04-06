<?php

namespace tiFy\App\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use tiFy\Contracts\App\AppServiceProvider as AppServiceProviderContract;
use tiFy\Container\ServiceProvider;

class AppServiceProvider extends ServiceProvider implements AppServiceProviderContract
{
    /**
     * Classe de rappel du controleur de l'interface associée.
     * @var PsrContainerInterface
     */
    protected $app;

    /**
     * Liste des alias de qualification de services.
     * @var array
     */
    protected $aliases = [];

    /**
     * Liste des services à instance multiples auto-déclarés.
     * @var string[]
     */
    protected $bindings = [];

    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [];

    /**
     * Liste des services à instance unique auto-déclarés.
     * @var string[]
     */
    protected $singletons = [];

    /**
     * @inheritdoc
     */
    public function boot()
    {

    }

    /**
     * @inheritdoc
     */
    public function getContainer()
    {
        return $this->app;
    }

    /**
     * @inheritdoc
     */
    public function setApp($app)
    {
        $this->container = $this->app = $app;

        $this->parse();
    }

    /**
     * @inheritdoc
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * @inheritdoc
     */
    public function getSingletons()
    {
        return $this->singletons;
    }

    /**
     * @inheritdoc
     */
    public function isSingleton($abstract)
    {
        $singletons = $this->getSingletons();

        return in_array($abstract, $singletons) || isset($singletons[$abstract]);
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        foreach ($this->aliases as $alias => $concrete) :
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

        foreach ($provides as $alias => $concrete) :
            array_push($this->provides, $concrete);

            if ($this->isSingleton($alias)) :
                $this->getContainer()->singleton($alias, $concrete);
            else :
                $this->getContainer()->bind($alias, $concrete);
            endif;
        endforeach;
    }
}