<?php

namespace tiFy\Contracts\Wp;

interface WpManager extends WpResolverTrait
{
    /**
     * Indicateur d'environnement Worpress.
     *
     * @return boolean
     */
    public function is();

    /**
     * Résolution d'un service fourni.
     *
     * @param string $alias Nom de qualification du service fourni.
     * @param array $args Liste des variables passées en argument.
     *
     * @return object|null
     */
    public function resolve($alias, $args = []);
}