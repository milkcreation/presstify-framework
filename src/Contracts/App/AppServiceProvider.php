<?php

namespace tiFy\Contracts\App;

use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Container\ServiceProvider\ServiceProviderInterface as LeagueServiceProviderInterface;

interface AppServiceProvider extends LeagueServiceProviderInterface, BootableServiceProviderInterface
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
     * @return AppContainer
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