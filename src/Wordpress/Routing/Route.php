<?php declare(strict_types=1);

namespace tiFy\Wordpress\Routing;

use tiFy\Wordpress\Contracts\Routing\Route as RouteContract;
use tiFy\Routing\Route as BaseRoute;

class Route extends BaseRoute implements RouteContract
{
    /**
     * Activation de la requête de récupération des éléments native de Wordpress.
     * @var boolean
     */
    protected $wpQuery = false;

    /**
     * @inheritDoc
     */
    public function isWpQuery(): bool
    {
        return $this->wpQuery;
    }

    /**
     * @inheritDoc
     */
    public function setWpQuery(bool $active = false): RouteContract
    {
        $this->wpQuery = $active;

        return $this;
    }
}