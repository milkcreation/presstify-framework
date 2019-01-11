<?php

namespace tiFy\Contracts\PostType;

interface PostType
{
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
     * Récupération d'une instance de controleur de type de post.
     *
     * @param string $name Nom de qualification du controleur.
     *
     * @return null|PostTypeFactory
     */
    public function get($name);
}