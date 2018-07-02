<?php

namespace tiFy\Apps\ServiceProvider;

interface ProviderItemInterface
{
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
     * Récupération du nom de qualification du service.
     *
     * @return string|int
     */
    public function getName();

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
     * Vérification d'existance d'une instanciation du controleur.
     *
     * @return bool
     */
    public function isInstanciated();

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
     * Définition de l'instanciation du controleur.
     *
     * @return bool
     */
    public function setInstanciated();
}