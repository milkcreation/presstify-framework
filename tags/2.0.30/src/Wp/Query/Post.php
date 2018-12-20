<?php

namespace tiFy\Wp\Query;

use tiFy\PostType\Query\PostQueryItem;

final class Post extends PostQueryItem
{
    /**
     * CONSTRUCTEUR.
     *
     * @param \WP_Post $wp_post Objet Post Wordpress.
     *
     * @return void
     */
    public function __construct(\WP_Post $wp_post)
    {
        parent::__construct($wp_post);
    }
}