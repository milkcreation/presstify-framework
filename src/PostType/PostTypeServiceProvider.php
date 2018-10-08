<?php

namespace tiFy\PostType;

use tiFy\App\Container\AppServiceProvider;
use tiFy\PostType\Metadata\Post as MetadataPost;
use tiFy\PostType\PostType;
use tiFy\PostType\PostTypeItemController;
use tiFy\PostType\PostTypeItemLabelsController;

class PostTypeServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $bindings = [
        PostTypeItemController::class,
        PostTypeItemLabelsController::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton(
            PostType::class,
            function () {
                return new PostType();
            }
        )->build();

        $this->app->singleton(
            MetadataPost::class
        )->build();
    }
}