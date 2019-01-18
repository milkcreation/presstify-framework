<?php

namespace tiFy\Wp\Query;

use tiFy\Contracts\Wp\Post as PostContract;
use tiFy\PostType\Query\PostQueryItem;

class Post extends PostQueryItem implements PostContract
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