<?php

namespace tiFy\Route;

use League\Route\Route as LeagueRoute;

class RouteController extends LeagueRoute implements RouteInterface
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