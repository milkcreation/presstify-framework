<?php

namespace tiFy\Taxonomy;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Taxonomy\Metadata\Term as MetadataTerm;
use tiFy\Taxonomy\Taxonomy;
use tiFy\Taxonomy\TaxonomyItemController;
use tiFy\Taxonomy\TaxonomyItemLabelsController;

class TaxonomyServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $bindings = [
        TaxonomyItemController::class,
        TaxonomyItemLabelsController::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton(
            Taxonomy::class,
            function () {
                return new Taxonomy();
            }
        )->build();

        $this->app->singleton(
            MetadataTerm::class
        )->build();
    }
}