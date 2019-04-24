<?php

namespace tiFy\Contracts\User;

use tiFy\User\Metadata\Metadata;
use tiFy\User\Metadata\Option;
use tiFy\User\Role\RoleManager;
use tiFy\User\Signin\SigninManager;
use tiFy\User\Signup\SignupManager;

interface UserManager
{
    /**
     * Récupération de l'instance de traitement des métadonnées utilisateur.
     *
     * @return Metadata
     */
    public function meta();

    /**
     * Récupération de l'instance de traitement des options utilisateur.
     *
     * @return Option
     */
    public function option();

    /**
     * Récupération de l'instance de traitement des roles utilisateur.
     *
     * @return RoleManager
     */
    public function role();

    /**
     * Récupération de l'instance de traitement des session utilisateur.
     *
     * @return SessionManager
     */
    public function session();

    /**
     * Récupération de l'instance de traitement des session utilisateur.
     *
     * @return SigninManager
     */
    public function signin();

    /**
     * Récupération de l'instance de traitement des session utilisateur.
     *
     * @return SignupManager
     */
    public function signup();

    /**
     * Résolution d'un service fourni par le conteneur d'injection de dépendances
     *
     * @param string $alias Nom de qualification du service.
     * @param mixed ...$args Liste des variables passées en argument au service
     *
     * @return mixed|object
     */
    public function resolve($alias, ...$args);
}
