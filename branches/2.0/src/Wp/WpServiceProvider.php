<?php

namespace tiFy\Wp;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Wp\Db\Db;
use tiFy\Wp\Filesystem\Filesystem;
use tiFy\Wp\Form\Form;
use tiFy\Wp\Media\MediaDownload;
use tiFy\Wp\Media\MediaManager;
use tiFy\Wp\Media\MediaUpload;
use tiFy\Wp\PageHook\PageHook;
use tiFy\Wp\PostType\PostType;
use tiFy\Wp\Query\Post as QueryPost;
use tiFy\Wp\Query\Posts as QueryPosts;
use tiFy\Wp\Query\Term as QueryTerm;
use tiFy\Wp\Query\Terms as QueryTerms;
use tiFy\Wp\Routing\Router;
use tiFy\Wp\Taxonomy\Taxonomy;
use tiFy\Wp\User\User;
use WP_Query;
use WP_Post;
use WP_Term;
use WP_Term_Query;

class WpServiceProvider extends AppServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        'wp',
        'wp.db',
        'wp.filesystem',
        'wp.form',
        'wp.page-hook',
        'wp.post-type',
        'wp.query.post',
        'wp.query.posts',
        'wp.query.term',
        'wp.query.terms',
        'wp.routing.router',
        'wp.taxonomy',
        'wp.user'
    ];

    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->app->bind('wp.ctags', function () { return new WpCtags(); });

        $this->app->singleton('wp.media.download', function () { return new MediaDownload(); })->build();
        $this->app->singleton('wp.media.manager', function () { return new MediaManager(); })->build();
        $this->app->singleton('wp.media.upload', function () { return new MediaUpload(); })->build();

        $this->app->singleton('wp.query', function () { return new WpQuery(); })->build();

        $this->app->bind('wp.screen', function (\WP_Screen $wp_screen) { return new WpScreen($wp_screen); });

        add_action('after_setup_tify', function () {
            /** @var WpManager $wp */
            $wp = $this->getContainer()->get('wp');

            if ($wp->is()) :
                $this->getContainer()->get('wp.page-hook');
                $this->getContainer()->get('wp.routing.router');

                if ($this->getContainer()->has('db')) :
                    $this->getContainer()->get('wp.db');
                endif;

                if ($this->getContainer()->has('storage')) :
                    $this->getContainer()->get('wp.filesystem');
                endif;

                if ($this->getContainer()->has('form')) :
                    $this->getContainer()->get('wp.form');
                endif;

                if ($this->getContainer()->has('post-type')) :
                    $this->getContainer()->get('wp.post-type');
                endif;

                if ($this->getContainer()->has('taxonomy')) :
                    $this->getContainer()->get('wp.taxonomy');
                endif;

                if ($this->getContainer()->has('template')) :
                    $this->getContainer()->get('template');
                endif;
            endif;
        });
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerManager();
        $this->registerDb();
        $this->registerFilesystem();
        $this->registerForm();
        $this->registerPageHook();
        $this->registerPostType();
        $this->registerQuery();
        $this->registerRouting();
        $this->registerTaxonomy();
        $this->registerUser();
    }

    /**
     * Déclaration du controleur de base de données.
     *
     * @return void
     */
    public function registerDb()
    {
        $this->getContainer()->share('wp.db',  function () {
            return new Db($this->getContainer()->get('db'));
        });
    }

    /**
     * Déclaration du controleur de système de fichiers.
     *
     * @return void
     */
    public function registerFilesystem()
    {
        $this->getContainer()->share('wp.filesystem',  function () {
            return new Filesystem($this->getContainer()->get('storage'));
        });
    }

    /**
     * Déclaration du controleur des formulaires.
     *
     * @return void
     */
    public function registerForm()
    {
        $this->getContainer()->share('wp.form',  function () {
            return new Form($this->getContainer()->get('form'));
        });
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
     * Déclaration du controleur des types de contenu.
     *
     * @return void
     */
    public function registerPostType()
    {
        $this->getContainer()->share('wp.post-type',  function () {
            return new PostType($this->getContainer()->get('post-type'));
        });
    }

    /**
     * Déclaration des controleurs de requête de récupération des éléments Wordpress.
     *
     * @return void
     */
    public function registerQuery()
    {
        $this->getContainer()->add('wp.query.posts', function(WP_Query $wp_query) {
            return new QueryPosts($wp_query);
        });

        $this->getContainer()->add('wp.query.post', function (WP_Post $wp_post) {
            return new QueryPost($wp_post);
        });

        $this->getContainer()->add('wp.query.terms', function(WP_Term_Query $wp_term_query) {
            return new QueryTerms($wp_term_query);
        });

        $this->getContainer()->add('wp.query.term', function (WP_Term $wp_term) {
            return new QueryTerm($wp_term);
        });
    }

    /**
     * Déclaration des controleurs de routage.
     *
     * @return void
     */
    public function registerRouting()
    {
        $this->getContainer()->share('wp.routing.router', Router::class);
    }

    /**
     * Déclaration du controleur des taxonomies.
     *
     * @return void
     */
    public function registerTaxonomy()
    {
        $this->getContainer()->share('wp.taxonomy',  function () {
            return new Taxonomy($this->getContainer()->get('taxonomy'));
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