<?php

namespace tiFy\Contracts\Routing;

use League\Route\Middleware\MiddlewareAwareInterface;
use League\Route\Strategy\StrategyAwareInterface;
use League\Route\RouteConditionHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

interface Route extends
    MiddlewareInterface,
    MiddlewareAwareInterface,
    RouteConditionHandlerInterface,
    StrategyAwareInterface
{
    /**
     * Récupération de l'url associée.
     *
     * @param array $params Liste des variables passée en argument. Tableau indexé.
     * @param boolean $absolute Activation de la récupération de l'url absolue.
     * 
     * @return string
     *
     * @throws \LogicException
     */
    public function getUrl($params = [], $absolute = true);

    /**
     * Récupération de la liste des arguments passée dans la requête HTTP courante.
     *
     * @return array
     */
    public function getVars();

    /**
     * Vérifie si la route répond à la requête HTTP courante.
     *
     * @return boolean
     */
    public function isCurrent();

    /**
     * Définition de l'indicateur de route en réponse à la requête courante.
     *
     * @return void
     */
    public function setCurrent();

    /**
     * Définition de la liste des variables passées en argument dans la requête HTTP courante.
     *
     * @param array $args Liste des variables.
     *
     * @return void
     */
    public function setVars(array $args);
}