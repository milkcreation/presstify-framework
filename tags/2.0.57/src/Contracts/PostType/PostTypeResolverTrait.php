<?php

namespace tiFy\Contracts\PostType;

interface PostTypeResolverTrait
{
    /**
     * Récupération de l'instance du controleur de metadonnées de post.
     *
     * @return PostTypePostMeta
     */
    public function post_meta();
}