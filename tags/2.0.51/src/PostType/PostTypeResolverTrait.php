<?php

namespace tiFy\PostType;

use tiFy\Contracts\PostType\PostTypeManager;

trait PostTypeResolverTrait
{
    /**
     * Instance du controleur principal.
     * @var PostTypeManager
     */
    protected $manager;

    /**
     * @inheritdoc
     */
    public function post_meta()
    {
        return $this->manager->resolve('post.meta');
    }
}