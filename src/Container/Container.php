<?php

namespace tiFy\Container;

use League\Container\Argument\RawArgument;
use League\Container\Container as LeagueContainer;
use League\Container\ServiceProvider\ServiceProviderInterface;
use tiFy\Contracts\Container\Container as ContainerContract;

class Container extends LeagueContainer implements ContainerContract
{
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

        foreach ($this->getServiceProviders() as $serviceProvider) :
            $resolved = $this->share($serviceProvider)->build();

            if ($resolved instanceof ServiceProviderInterface) :
                $resolved->setContainer($this);
                $this->addServiceProvider($resolved);
            endif;
        endforeach;

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
     * @inheritdoc
     */
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }

    /**
     * @inheritdoc
     */
    public function make($alias, $args = [])
    {
        return $this->get($alias, $args);
    }

    /**
     * @inheritdoc
     */
    public function hasParameter()
    {
        return false;
    }
}