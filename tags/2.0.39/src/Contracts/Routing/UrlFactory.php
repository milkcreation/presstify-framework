<?php

namespace tiFy\Contracts\Routing;

interface UrlFactory
{
    /**
     * Résolution de sortie sous forme de chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Formatage d'une url au format RFC3986|RFC3987.
     *
     * @param string $format RFC3986|RFC3987.
     *
     * @return static
     */
    public function format($format = 'RFC3986');

    /**
     * Récupération de l'url traitée.
     *
     * @return string
     */
    public function get();

    /**
     * Ajout d'arguments à l'url.
     *
     * @param array $args Liste des arguments de requête à inclure.
     *
     * @return static
     */
    public function with(array $args);

    /**
     * Suppression d'arguments de l'url.
     *
     * @param string[] $args Liste des arguments de requête à exclure.
     *
     * @return static
     */
    public function without(array $args);
}