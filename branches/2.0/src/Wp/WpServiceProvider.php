<?php

namespace tiFy\Wp;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Wp\Media\MediaDownload;
use tiFy\Wp\Media\MediaManager;
use tiFy\Wp\Media\MediaUpload;
use tiFy\Wp\PageHook\PageHook;
use tiFy\Wp\PostType\WpPostTypeManager;
use tiFy\Wp\Query\Post;
use tiFy\Wp\Query\Posts;
use tiFy\Wp\Routing\Router;
use tiFy\Wp\Taxonomy\WpTaxonomyManager;
use tiFy\Wp\User\User;

class WpServiceProvider extends AppServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        'wp',
        'wp.page-hook',
        'wp.post-type',
        'wp.routing.router',
        'wp.taxonomy',
        'wp.user'
    ];

    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->registerManager();

        $this->app->bind('wp.ctags', function () { return new WpCtags(); });

        $this->app->singleton('wp.media.download', function () { return new MediaDownload(); })->build();
        $this->app->singleton('wp.media.manager', function () { return new MediaManager(); })->build();
        $this->app->singleton('wp.media.upload', function () { return new MediaUpload(); })->build();

        $this->app->singleton('wp.query', function () { return new WpQuery(); })->build();

        $this->registerQuery();

        $this->app->bind('wp.screen', function (\WP_Screen $wp_screen) { return new WpScreen($wp_screen); });

        add_action('after_setup_tify', function () {
            $this->getContainer()->get('wp');

            $this->getContainer()->get('wp.page-hook');
            $this->getContainer()->get('wp.routing.router');

            if ($this->getContainer()->has('post-type')) :
                $this->getContainer()->get('wp.post-type');
            endif;

            if ($this->getContainer()->has('taxonomy')) :
                $this->getContainer()->get('wp.taxonomy');
            endif;

            if ($this->getContainer()->has('template')) :
                $this->getContainer()->get('template');
            endif;
        });
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerPageHook();
        $this->registerPostType();
        $this->registerRouting();
        $this->registerTaxonomy();
        $this->registerUser();
    }

    /**
     * Déclaration du controleur de gestion de Wordpress.
     *
     * @return void
     */
    public function registerManager()
    {
        $this->getContainer()->share('wp', WpManager::class);
    }

    /**
     * Déclaration du controleur des pages d'accroche.
     *
     * @return void
     */
    public function registerPageHook()
    {
        $this->getContainer()->share('wp.page-hook', PageHook::class);
    }

    /**
     * Déclaration du controleur des type de contenu.
     *
     * @return void
     */
    public function registerPostType()
    {
        $this->getContainer()->share('wp.post-type',  function () {
            return new WpPostTypeManager($this->getContainer()->get('post-type'));
        });
    }

    /**
     * Déclaration des controleurs de requête de récupération des éléments Wordpress.
     *
     * @return void
     */
    public function registerQuery()
    {
        $this->app->bind('wp.query.post', function (\WP_Post $wp_post) { return new Post($wp_post); });
        $this->app->bind('wp.query.posts', function (\WP_Query $wp_query) { return new Posts($wp_query); });
    }

    /**
     * Déclaration des controleurs de routage.
     *
     * @return void
     */
    public function registerRouting()
    {
        $this->getContainer()->share('wp.routing.router', function () { return new Router(); });
    }

    /**
     * Déclaration du controleur des taxonomies.
     *
     * @return void
     */
    public function registerTaxonomy()
    {
        $this->getContainer()->share('wp.taxonomy',  function () {
            return new WpTaxonomyManager($this->getContainer()->get('taxonomy'));
        });
    }

    /**
     * Déclaration du controleur des pages d'accroche.
     *
     * @return void
     */
    public function registerUser()
    {
        $this->getContainer()->add('wp.user', User::class);
    }
}