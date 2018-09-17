<?php

namespace tiFy\Contracts\PostType;

use tiFy\Contracts\Kernel\ParametersBagInterface;
use \WP_Post_Type;

interface PostTypeItemInterface extends ParametersBagInterface
{
    /**
     * Récupération du nom de qualification du type de post.
     *
     * @return string
     */
    public function getName();

    /**
     * Déclaration du type de post.
     *
     * @return WP_Post_Type
     */
    public function register();
}