<?php

namespace tiFy\Contracts\App;

use League\Container\Definition\DefinitionInterface;

interface AppService
{
    /**
     * Déclaration du service dans le conteneur.
     *
     * @return DefinitionInterface
     */
    public function bind();

    /**
     * Résolution d'instance du service.
     *
     * @param array $args Liste des variables passées en argument.
     *
     * @return mixed
     */
    public function build($args = []);

    /**
     * Récupération du nom de qualification de récupération.
     *
     * @return string
     */
    public function getAbstract();

    /**
     * Récupération de l'alias.
     *
     * @return string
     */
    public function getAlias();

    /**
     * Récupération de la liste des variables passées en argument.
     *
     * @return array
     */
    public function getArgs();

    /**
     * Récupération du controleur de service.
     *
     * @return callable|object
     */
    public function getConcrete();

    /**
     * Récupération de la classe de rappel du conteneur de services.
     *
     * @return AppContainer
     */
    public function getContainer();

    /**
     * Vérifie si un controleur de service est instancié au démarrage.
     *
     * @return bool
     */
    public function isBootable();

    /**
     * Vérifie si un controleur de service est une fonction anonyme.
     *
     * @return bool
     */
    public function isClosure();

    /**
     * Vérifie si un controleur de service est instancié de manière différé.
     *
     * @return bool
     */
    public function isDeferred();

    /**
     * Vérifie si le controleur de service doit être traité comme un singleton.
     * @internal Une seule instance unique de la classe dans l'écosystème
     *
     * @return bool
     */
    public function isSingleton();

    /**
     * Définition de la liste des variables passées en argument.
     *
     * @param array $args Liste des variables passées au controleur en argument
     *
     * @return $this
     */
    public function setArgs($args = []);

    /**
     * Vérification d'existance d'une instance
     *
     * @return bool
     */
    public function resolved();
}