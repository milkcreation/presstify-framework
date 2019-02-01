<?php

namespace tiFy\Contracts\Taxonomy;

interface TaxonomyManager extends TaxonomyResolverTrait
{
    /**
     * Récupération d'une instance de controleur de taxonomie.
     *
     * @param string $name Nom de qualification du controleur.
     *
     * @return null|TaxonomyFactory
     */
    public function get($name);

    /**
     * Création d'une taxonomie personnalisée.
     *
     * @param string $name Nom de qualification de la taxonomie.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return TaxonomyFactory
     */
    public function register($name, $attrs = []);

    /**
     * Résolution d'un service fourni par le gestionnaire.
     *
     * @param string $alias Nom de qualification du service.
     * @param array $args Liste des variables passées en argument au service.
     *
     * @return object
     */
    public function resolve($alias, $args = []);
}