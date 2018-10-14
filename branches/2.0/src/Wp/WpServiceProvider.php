<?php

namespace tiFy\Wp;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Wp\WpCtags;
use tiFy\Wp\WpQuery;
use tiFy\Wp\WpScreen;
use tiFy\Wp\WpTaxonomy;

class WpServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->bind('wp.ctags', function () { return new WpCtags(); });
        $this->app->singleton('wp.query', function () { return new WpQuery(); })->build();
        $this->app->bind('wp.screen', function (\WP_Screen $wp_screen) { return new WpScreen($wp_screen); });
        $this->app->bind('wp.taxonomy', function () { return new WpTaxonomy(); });
    }
}