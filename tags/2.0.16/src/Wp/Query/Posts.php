<?php

namespace tiFy\Wp\Query;

use tiFy\PostType\Query\PostQueryCollection;

final class Posts extends PostQueryCollection
{
    /**
     * CONSTRUCTEUR.
     *
     * @param null|\WP_Query $wp_query Requête Wordpress de récupération de post. Par défaut, utilise la requête globale de Wordpress.
     *
     * @return void
     */
    public function __construct($wp_query = null)
    {
        $posts = [];

        if (is_null($wp_query)) :
            global $wp_query;
        endif;

        if ($wp_query instanceof \WP_Query) :
            foreach($wp_query->posts as $post) :
                $posts[] = app('wp.query.post', [$post]);
            endforeach;
        endif;

        parent::__construct($posts);
    }
}