<?php

namespace tiFy\Routing;

use League\Route\Route as LeagueRoute;
use tiFy\Contracts\Routing\Route as RouteContract;

class Route extends LeagueRoute implements RouteContract
{
    /**
     * Indicateur de route en réponse à la requête HTTP courante.
     * @var boolean
     */
    protected $current = false;

    /**
     * Liste des variables passées en arguments.
     * @var array
     */
    protected $args = [];

    /**
     * {@inheritdoc}
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * {@inheritdoc}
     */
    public function isCurrent()
    {
        return $this->current();
    }

    /**
     * {@inheritdoc}
     */
    public function setArgs($args = [])
    {
        $this->args = $args;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrent()
    {
        $this->current = true;
    }
}