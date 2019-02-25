<?php

namespace tiFy\Wp\Query;

use tiFy\PostType\Query\PostQueryCollection;
use WP_Post;
use WP_Query;

class Posts extends PostQueryCollection
{
    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Query $wp_query Requête Wordpress de récupération de post.
     *
     * @return void
     */
    public function __construct(WP_Query $wp_query)
    {
        parent::__construct($wp_query instanceof WP_Query ? $wp_query->posts : []);
    }

    /**
     * Récupération d'une instance basée sur la liste des arguments.
     * @see https://codex.wordpress.org/Class_Reference/WP_Query
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @param array $args Liste des arguments de la requête récupération des éléments
     *
     * @return static
     */
    public static function createFromArgs($args = [])
    {
        return new static(new WP_Query($args));
    }

    /**
     * Récupération d'une instance basée sur la liste des arguments.
     * @see https://codex.wordpress.org/Class_Reference/WP_Query
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @return static
     */
    public static function createFromGlobals()
    {
        global $wp_query;

        return new static($wp_query);
    }

    /**
     * {@inheritdoc}
     *
     * @param WP_Post $item Objet post Wordpress.
     *
     * @return void
     */
    public function wrap($item, $key = null)
    {
        $this->items[$key] = app()->get('wp.query.post', [$item]);
    }
}