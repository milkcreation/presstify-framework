<?php

namespace tiFy\Apps\Container;

use tiFy\Apps\AppControllerInterface;

interface ContainerInterface extends AppControllerInterface
{
    /**
     * Récupération d'un service fourni.
     *
     * @param string $alias Identifiant de qualification du controleur.
     * @param array $args Liste des variables passés en argument.
     *
     * @return object
     */
    public function resolve($alias, $args = []);
}