<?php

namespace tiFy\Apps\ServiceProvider;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\Exception\NotFoundException;
use League\Container\ServiceProvider\ServiceProviderInterface;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use LogicException;
use ReflectionFunction;
use ReflectionException;
use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\ServiceProvider\ProviderItem;

interface ProviderCollectionInterface extends ServiceProviderInterface, BootableServiceProviderInterface
{
    /**
     * Déclaration du fournisseur de service
     *
     * @param ProviderItem $item
     *
     * @return void
     */
    public function add($item);

    /**
     * Déclaration des services instanciés au démarrage.
     *
     * @return void
     */
    public function boot();

    /**
     * Liste des service par défaut.
     *
     * @return void
     */
    public function defaults();

    /**
     * Récupération d'un fournisseur de service.
     *
     * @param string $key Clé d'index de qualification du service.
     * @param null|array $args Liste des variables passée en argument.
     *
     * @return null|object
     */
    public function get($key, $args = null);

    /**
     * Récupération de la liste des controleurs au démarrage.
     *
     * @return array
     */
    public function getBootable();

    /**
     * Récupération de la classe de rappel du conteneur d'injection utilisé par le fournisseur de service.
     *
     * @return ContainerInterface|Container
     */
    public function getContainer();

    /**
     * Récupération de la liste des controleurs différés.
     *
     * @return array
     */
    public function getDeferred();

    /**
     * Vérification d'existance d'un controleur de service
     *
     * @return bool
     */
    public function has($key);

    /**
     * Traitement de la liste des services.
     *
     * @param array $items
     *
     * @return void
     */
    public function parse($items);

    /**
     * Traitement du service selon sa clé d'indice de qualification.
     *
     * @param string $key Clé d'indice de qualification du service.
     * @param string|callable Valeur de retour par défaut.
     *
     * @return string|callable
     */
    public function parseConcrete($key, $default);

    /**
     * Déclaration des services instanciés de manière différées.
     *
     * @return void
     */
    public function register();
}