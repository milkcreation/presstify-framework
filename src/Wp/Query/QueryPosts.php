<?php

namespace tiFy\Wp\Query;

use tiFy\Contracts\Wp\QueryPost as QueryPostContract;
use tiFy\Contracts\Wp\QueryPosts as QueryPostsContract;
use tiFy\Support\Collection;
use WP_Post;
use WP_Query;

class QueryPosts extends Collection implements QueryPostsContract
{
    /**
     * Classe de rappel de traitement d'un élément.
     * @var QueryPostContract
     */
    protected $itemClass = QueryPost::class;

    /**
     * Instance de la requête Wordpress de récupération des posts.
     * @var WP_Query
     */
    protected $wp_query;

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Query $wp_query Requête Wordpress de récupération de post.
     *
     * @return void
     */
    public function __construct(WP_Query $wp_query)
    {
        $this->wp_query = $wp_query;

        $items = $wp_query->posts;
        array_walk($items, [$this, 'walk']);
    }

    /**
     * @inheritdoc
     */
    public static function createFromArgs($args = [])
    {
        return new static(new WP_Query($args));
    }

    /**
     * @inheritdoc
     */
    public static function createFromGlobals()
    {
        global $wp_query;

        return new static($wp_query);
    }

    /**
     * @inheritdoc
     */
    public static function createFromPost($post = null)
    {
        if ($post instanceof WP_Post) :
            return new static(new WP_Query(['p' => $post->ID, 'post_type' => 'any']));
        elseif (is_numeric($post)) :
            return new static(new WP_Query(['p' => $post, 'post_type' => 'any']));
        elseif (is_null($post) && is_singular()) :
            return static::createFromGlobals();
        endif;

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getIds()
    {
        return $this->pluck('ID');
    }

    /**
     * @inheritdoc
     */
    public function getTitles()
    {
        return $this->pluck('post_title');
    }

    /**
     * {@inheritdoc}
     *
     * @param WP_Post $item Objet post Wordpress.
     *
     * @return void
     */
    public function walk($item, $key = null)
    {
        $this->items[$key] = new $this->itemClass($item);
    }

    /**
     * @inheritdoc
     */
    public function WpQuery()
    {
        return $this->wp_query;
    }
}