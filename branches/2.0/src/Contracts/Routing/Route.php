<?php

namespace tiFy\Contracts\Routing;

use League\Route\ContainerAwareInterface;
use League\Route\Middleware\StackAwareInterface as MiddlewareAwareInterface;
use League\Route\Strategy\StrategyAwareInterface;

interface Route extends ContainerAwareInterface, MiddlewareAwareInterface, StrategyAwareInterface
{
    /**
     * Récupération de la liste des arguments passée dans la requête HTTP courante.
     *
     * @return array
     */
    public function getArgs();

    /**
     * Vérifie si la route répond à la requête HTTP courante.
     *
     * @return boolean
     */
    public function isCurrent();

    /**
     * Définition de la liste des variables passées en argument dans la requête HTTP courante.
     *
     * @param array $args Liste des variables.
     *
     * @return void
     */
    public function setArgs($args = []);

    /**
     * Définition de l'indicateur de route en réponse à la requête courante.
     *
     * @return void
     */
    public function setCurrent();
}