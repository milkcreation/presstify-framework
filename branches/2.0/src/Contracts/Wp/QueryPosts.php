<?php

namespace tiFy\Contracts\Wp;

use tiFy\Contracts\Support\Collection;
use WP_Post;
use WP_Query;

interface QueryPosts extends Collection
{
    /**
     * Récupération d'une instance basée sur une liste des arguments.
     * @see https://codex.wordpress.org/Class_Reference/WP_Query
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @param array $args Liste des arguments de la requête récupération des éléments.
     *
     * @return static
     */
    public static function createFromArgs($args = []);

    /**
     * Récupération d'une instance basée sur la requête globale.
     * @see https://codex.wordpress.org/Class_Reference/WP_Query
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @return static
     */
    public static function createFromGlobals();

    /**
     * Récupération de la liste des identifiants de qualification.
     *
     * @return array
     */
    public function getIds();

    /**
     * Récupération de la liste des intitulés de qualification.
     *
     * @return array
     */
    public function getTitles();

    /**
     * Récupération de l'instance de la requête Wordpress de récupération des posts.
     *
     * @return null|WP_Query
     */
    public function WpQuery();
}