<?php declare(strict_types=1);

namespace tiFy\Wordpress\Contracts\Routing;

use tiFy\Contracts\Routing\Route as BaseRoute;

interface Route extends BaseRoute
{
    /**
     * Vérification de l'activation de la requête de récupération des éléments native de Wordpress.
     *
     * @param boolean $active
     *
     * @return static
     */
    public function isWpQuery(): bool;

    /**
     * Définition de l'activation de la requête de récupération des éléments native de Wordpress.
     *
     * @param boolean $active
     *
     * @return static
     */
    public function setWpQuery(bool $active = false): Route;
}