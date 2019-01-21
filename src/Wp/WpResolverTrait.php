<?php

namespace tiFy\Wp;

trait WpResolverTrait
{
    /**
     * Instance du controleur principal.
     * @var WpManager
     */
    protected $manager;

    /**
     * @inheritdoc
     */
    public function post_type()
    {
        return $this->manager->resolve('post_type');
    }

    /**
     * @inheritdoc
     */
    public function taxonomy()
    {
        return $this->manager->resolve('taxonomy');
    }

    /**
     * @inheritdoc
     */
    public function user()
    {
        return $this->manager->resolve('user');
    }
}