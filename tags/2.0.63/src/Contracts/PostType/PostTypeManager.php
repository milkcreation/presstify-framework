<?php

namespace tiFy\Contracts\PostType;

interface PostTypeManager extends PostTypeResolverTrait
{
    /**
     * Récupération d'une instance de controleur de type de post.
     *
     * @param string $name Nom de qualification du controleur.
     *
     * @return null|PostTypeFactory
     */
    public function get($name);

    /**
     * Création d'un type de post personnalisé.
     *
     * @param string $name Nom de qualification du type de post.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return PostTypeFactory
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