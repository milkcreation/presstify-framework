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
use tiFy\Apps\ServiceProvider\ProviderItem;

interface ServiceProviderInterface extends LeagueServiceProviderInterface, BootableServiceProviderInterface
{
    /**
     * Liste des service par défaut.
     *
     * @return void
     */
    public function defaults();

    /**
     * Récupération de la classe de rappel du conteneur d'injection utilisé par le fournisseur de service.
     *
     * @return ContainerInterface|Container
     */
    public function getContainer();


    /**
     * Traitement de la liste des services.
     *
     * @param array $items
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