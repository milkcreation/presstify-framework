<?php

namespace tiFy\App\Container;

interface ContainerInterface
{
    /**
     * Vérifie de disponibilité d'un service.
     *
     * @param string $abstract Nom ou alias de qualification du service.
     *
     * @return bool
     */
    public function bound($abstract);

    /**
     * Déclaration d'un service.
     *
     * @param string $abstract Nom de qualification du service.
     * @param string|object|callable $concrete Nom de classe|Instance de classe|fonction anonyme.
     * @param bool $singleton Indicateur d'instance unique.
     *
     * @return void
     */
    public function bind($abstract, $concrete = null, $singleton = false);

    /**
     * Récupération d'un alias de service déclaré.
     *
     * @return string
     */
    public function getAlias($concrete);

    /**
     * Récupération de la liste des alias de services déclarés.
     *
     * @return array
     */
    public function getAliases();

    /**
     * Récupération du nom de qualification de récupération d'un service.
     *
     * @param string $abstract Nom ou alias de qualification du service.
     *
     * @return string
     */
    public function getAbstract($abstract);

    /**
     * Récupération d'un service déclaré.
     *
     * @param string $abstract Nom ou alias de qualification du service.
     *
     * @return Service
     */
    public function getService($abstract);

    /**
     * Récupération de la liste des fournisseurs de services ou services indépendants déclaré.
     *
     * @return array
     */
    public function getServiceProviders();

    /**
     * Récupération d'une instance de service.
     *
     * @param string $abstract Nom ou alias de qualification du service.
     * @param array $args Liste des variables passées en argument.
     *
     * @return object
     */
    public function resolve($abstract, $args = []);

    /**
     * Déclaration d'un service à instance unique.
     *
     * @param string $abstract Nom de qualification du service.
     * @param string|object|callable $concrete Nom de classe|Instance de classe|fonction anonyme.
     *
     * @return void
     */
    public function singleton($abstract, $concrete = null);
}