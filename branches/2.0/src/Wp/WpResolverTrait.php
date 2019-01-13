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