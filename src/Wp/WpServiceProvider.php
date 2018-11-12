<?php

namespace tiFy\Wp;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Wp\Media\MediaDownload;
use tiFy\Wp\Media\MediaManager;
use tiFy\Wp\Query\Post;
use tiFy\Wp\Query\Posts;
use tiFy\Wp\WpCtags;
use tiFy\Wp\WpQuery;
use tiFy\Wp\WpScreen;
use tiFy\Wp\WpTaxonomy;
use tiFy\Wp\WpUser;

class WpServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->bind('wp.ctags', function () { return new WpCtags(); });

        $this->app->singleton('wp.media.download', function () { return new MediaDownload(); })->build();
        $this->app->singleton('wp.media.manager', function () { return new MediaManager(); })->build();

        $this->app->singleton('wp.query', function () { return new WpQuery(); })->build();

        $this->registerQuery();

        $this->app->bind('wp.screen', function (\WP_Screen $wp_screen) { return new WpScreen($wp_screen); });

        $this->app->bind('wp.taxonomy', function () { return new WpTaxonomy(); });

        $this->app->bind('wp.user', function () { return new WpUser(); });
    }


    /**
     * Déclaration des controleurs de requête de récupération des éléments Wordpress.
     *
     * @return void
     */
    public function registerQuery()
    {
        $this->app->bind('wp.query.post', function (\WP_Post $wp_post) { return new Post($wp_post);});
        $this->app->bind('wp.query.posts', function (\WP_Query $wp_query) { return new Posts($wp_query); });
    }
}