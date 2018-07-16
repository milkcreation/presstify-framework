<?php

namespace tiFy\Apps\Container;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\Exception\NotFoundException;
use League\Container\ServiceProvider\ServiceProviderInterface as LeagueServiceProviderInterface;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use LogicException;
use ReflectionFunction;
use ReflectionException;
use tiFy\Apps\AppControllerInterface;

interface ServiceProviderInterface extends LeagueServiceProviderInterface, BootableServiceProviderInterface
{
    /**
     * Récupération de la liste des services à instances multiples auto-déclarés.
     *
     * @return array
     */
    public function getBindings();

    /**
     * Récupération de la classe de rappel du conteneur d'injection utilisé par le fournisseur de service.
     *
     * @return ContainerInterface|Container
     */
    public function getContainer();

    /**
     * Récupération de la liste des services à instances unique auto-déclarés.
     *
     * @return array
     */
    public function getSingletons();

    /**
     * Vérifie si le nom de qualification répond à un service à instance unique auto-déclarés.
     *
     * @param string $abstract Nom de qualification d'appel du service.
     *
     * @return array
     */
    public function isSingleton($abstract);

    /**
     * Traitement de la liste des services.
     *
     * @return void
     */
    public function parse();
    
    /**
     * Déclaration des services instanciés de manière différées.
     *
     * @return void
     */
    public function register();
}