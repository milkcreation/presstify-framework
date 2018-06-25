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
     * Vérifie si un controleur de service est une fonction anonyme.
     *
     * @return bool
     */
    public function isClosure();

    /**
     * Vérifie si le controleur de service doit être traité comme un singleton.
     * @internal Une seule instance unique de la classe dans l'écosystème
     *
     * @return bool
     */
    public function isSingleton();
}