<?php

namespace tiFy\Taxonomy;

use tiFy\Contracts\Taxonomy\TaxonomyManager;

trait TaxonomyResolverTrait
{
    /**
     * Instance du controleur principal.
     * @var TaxonomyManager
     */
    protected $manager;

    /**
     * @inheritdoc
     */
    public function term_meta()
    {
        return $this->manager->resolve('term.meta');
    }
}