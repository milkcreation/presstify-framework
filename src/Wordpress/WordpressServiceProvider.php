<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Asset\AssetManagerInterface;
use Pollen\Debug\DebugManagerInterface;
use Pollen\Routing\RouterInterface;
use Pollen\Session\SessionManagerInterface;
use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Form\FormManager;
use tiFy\Field\Contracts\FieldContract;
use tiFy\Metabox\Contracts\MetaboxContract;
use tiFy\Partial\Contracts\PartialContract;
use tiFy\Support\Locale;
use tiFy\Wordpress\Auth\Auth;
use tiFy\Wordpress\Column\Column;
use tiFy\Wordpress\Cookie\Cookie;
use tiFy\Wordpress\Database\Database;
use tiFy\Wordpress\Filesystem\Filesystem;
use tiFy\Wordpress\Field\Field;
use tiFy\Wordpress\Form\Form;
use tiFy\Wordpress\Http\Http;
use tiFy\Wordpress\Mail\Mailer;
use tiFy\Wordpress\Media\Media;
use tiFy\Wordpress\Metabox\Metabox;
use tiFy\Wordpress\Option\Option;
use tiFy\Wordpress\PageHook\PageHook;
use tiFy\Wordpress\PageHook\PageHookMetabox;
use tiFy\Wordpress\Partial\Partial;
use tiFy\Wordpress\PostType\PostType;
use tiFy\Wordpress\Query\QueryPost;
use tiFy\Wordpress\Query\QueryTerm;
use tiFy\Wordpress\Query\QueryUser;
use tiFy\Wordpress\Taxonomy\Taxonomy;
use tiFy\Wordpress\Template\Template;
use tiFy\Wordpress\User\User;
use tiFy\Wordpress\User\Role\RoleFactory;
use tiFy\Wordpress\View\View;
use WP_Post, WP_Screen, WP_Term, WP_User;

class WordpressServiceProvider extends ServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        'wp.asset',
        'wp.auth',
        'wp.column',
        'wp.cookie',
        'wp.database',
        'wp.db',
        'wp.debug',
        'wp.filesystem',
        'wp.field',
        'wp.form',
        'wp.http',
        'wp.login-redirect',
        'wp.mailer',
        'wp.media',
        'wp.metabox',
        'wp.page-hook',
        PageHookMetabox::class,
        'wp.partial',
        'wp.option',
        'wp.post-type',
        'wp.query.post',
        'wp.query.term',
        'wp.query.user',
        'wp.routing',
        'wp.session',
        'wp.taxonomy',
        'wp.template',
        'wp.user',
        'wp.wp_query',
        'wp.wp_screen',
        'wp.view',
    ];

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        require_once __DIR__ . '/helpers.php';

        $this->getContainer()->share('wp', $wp = new Wordpress());

        add_action('plugins_loaded', function () {
            load_muplugin_textdomain('tify', '/presstify/languages/');
            do_action('tify_load_textdomain');
        });

        add_action('after_setup_theme', function () use ($wp) {
            if ($wp->is()) {
                require_once(ABSPATH . 'wp-admin/includes/translation-install.php');
                Locale::set(get_locale());
                Locale::setLanguages(wp_get_available_translations() ?: []);

                if ($this->getContainer()->has(DebugManagerInterface::class)) {
                    $this->getContainer()->get('wp.debug');
                }

                if ($this->getContainer()->has(RouterInterface::class)) {
                    $this->getContainer()->get('wp.routing');
                }

                if ($this->getContainer()->has(AssetManagerInterface::class)) {
                    $this->getContainer()->get('wp.asset');
                }

                $this->getContainer()->get('wp.auth');

                if ($this->getContainer()->has('column')) {
                    $this->getContainer()->get('wp.column');
                }

                if ($this->getContainer()->has('cookie')) {
                    $this->getContainer()->get('wp.cookie');
                }

                if ($this->getContainer()->has('cron')) {
                    $this->getContainer()->get('cron');
                }

                if ($this->getContainer()->has('database')) {
                    $this->getContainer()->get('wp.database');
                }

                if ($this->getContainer()->has('db')) {
                    $this->getContainer()->get('wp.db');
                }

                if ($this->getContainer()->has(FieldContract::class)) {
                    $this->getContainer()->get('wp.field');
                }

                if ($this->getContainer()->has(FormManager::class)) {
                    $this->getContainer()->get('wp.form');
                }

                $this->getContainer()->get('wp.http');

                if ($this->getContainer()->has('mailer')) {
                    $this->getContainer()->get('wp.mailer');
                }

                $this->getContainer()->get('wp.media');

                if ($this->getContainer()->has(MetaboxContract::class)) {
                    $this->getContainer()->get('wp.metabox');
                }

                $this->getContainer()->get('wp.page-hook');

                $this->getContainer()->get('wp.option');

                if ($this->getContainer()->has(PartialContract::class)) {
                    $this->getContainer()->get('wp.partial');
                }

                if ($this->getContainer()->has('post-type')) {
                    $this->getContainer()->get('wp.post-type');
                }

                if ($this->getContainer()->has(SessionManagerInterface::class)) {
                    $this->getContainer()->get('wp.session');
                }

                if ($this->getContainer()->has('storage')) {
                    $this->getContainer()->get('wp.filesystem');
                }

                if ($this->getContainer()->has('taxonomy')) {
                    $this->getContainer()->get('wp.taxonomy');
                }

                if ($this->getContainer()->has('template')) {
                    $this->getContainer()->get('wp.template');
                }

                if ($this->getContainer()->has('user')) {
                    $this->getContainer()->get('wp.user');
                    $this->getContainer()->add('user.role.factory', function () {
                        return new RoleFactory();
                    });
                }

                if ($this->getContainer()->has('view')) {
                    $this->getContainer()->get('wp.view');
                }

                events()->trigger('wp.booted');
            }
        }, 1);
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->registerAsset();
        $this->registerAuth();
        $this->registerColumn();
        $this->registerCookie();
        $this->registerDatabase();
        $this->registerDebug();
        $this->registerFilesystem();
        $this->registerField();
        $this->registerForm();
        $this->registerHttp();
        $this->registerMailer();
        $this->registerMedia();
        $this->registerMetabox();
        $this->registerOptions();
        $this->registerPageHook();
        $this->registerPartial();
        $this->registerPostType();
        $this->registerQuery();
        $this->registerRouting();
        $this->registerSession();
        $this->registerTaxonomy();
        $this->registerTemplate();
        $this->registerUser();
        $this->registerView();
    }

    /**
     * Déclaration du gestionnaire d'assets.
     *
     * @return void
     */
    public function registerAsset(): void
    {
        $this->getContainer()->share('wp.asset', function () {
            return new WpAsset($this->getContainer()->get(AssetManagerInterface::class));
        });
    }
    /**
     * Déclaration du gestionnaire d'authentification.
     *
     * @return void
     */
    public function registerAuth(): void
    {
        $this->getContainer()->share('wp.auth', function () {
            return new Auth();
        });
    }


    /**
     * Déclaration du controleur des colonnes.
     *
     * @return void
     */
    public function registerColumn(): void
    {
        $this->getContainer()->share('wp.column', function () {
            return new Column($this->getContainer()->get('column'));
        });
    }

    /**
     * Déclaration du controleur des cookies.
     *
     * @return void
     */
    public function registerCookie(): void
    {
        $this->getContainer()->share('wp.cookie', function () {
            return new Cookie($this->getContainer()->get('cookie'));
        });
    }

    /**
     * Déclaration du controleur de base de données.
     *
     * @return void
     */
    public function registerDatabase(): void
    {
        $this->getContainer()->share('wp.database', function () {
            return new Database($this->getContainer()->get('database'));
        });
    }

    /**
     * Déclaration du gestionnaire de deboguage.
     *
     * @return void
     */
    public function registerDebug(): void
    {
        $this->getContainer()->share('wp.debug', function () {
            return new WpDebug($this->getContainer()->get(DebugManagerInterface::class));
        });
    }

    /**
     * Déclaration du controleur de système de fichiers.
     *
     * @return void
     */
    public function registerFilesystem(): void
    {
        $this->getContainer()->share('wp.filesystem', function () {
            return new Filesystem($this->getContainer()->get('storage'));
        });
    }

    /**
     * Déclaration du controleur des champs.
     *
     * @return void
     */
    public function registerField(): void
    {
        $this->getContainer()->share('wp.field', function () {
            return new Field($this->getContainer()->get(FieldContract::class), $this->getContainer());
        });
    }

    /**
     * Déclaration du controleur des formulaires.
     *
     * @return void
     */
    public function registerForm(): void
    {
        $this->getContainer()->share('wp.form', function () {
            return new Form($this->getContainer()->get(FormManager::class), $this->getContainer());
        });
    }

    /**
     * Déclaration du controleur des processus HTTP. Requête, Réponse, Session ...
     *
     * @return void
     */
    public function registerHttp(): void
    {
        $this->getContainer()->share('wp.http', function () {
            return new Http($this->getContainer()->get('request'));
        });
    }

    /**
     * Déclaration du controleur de gestion de Wordpress.
     *
     * @return void
     */
    public function registerMailer(): void
    {
        $this->getContainer()->share('wp.mailer', function () {
            return new Mailer($this->getContainer()->get('mailer'));
        });
    }

    /**
     * Déclaration du controleur de gestion des Medias.
     *
     * @return void
     */
    public function registerMedia(): void
    {
        $this->getContainer()->share('wp.media', function () {
            return new Media();
        });
    }

    /**
     * Déclaration du controleur de gestion de metaboxes.
     *
     * @return void
     */
    public function registerMetabox(): void
    {
        $this->getContainer()->share('wp.metabox', function () {
            return new Metabox($this->getContainer()->get(MetaboxContract::class), $this->getContainer());
        });
    }

    /**
     * Déclaration du controleur des options
     *
     * @return void
     */
    public function registerOptions(): void
    {
        $this->getContainer()->share('wp.option', function () {
            return new Option();
        });
    }

    /**
     * Déclaration du controleur des pages d'accroche.
     *
     * @return void
     */
    public function registerPageHook(): void
    {
        $this->getContainer()->share('wp.page-hook', function () {
            return new PageHook();
        });
        $this->getContainer()->add(PageHookMetabox::class, function () {
            return new PageHookMetabox(
                $this->getContainer()->get('wp.page-hook'),
                $this->getContainer()->get(MetaboxContract::class)
            );
        });
    }

    /**
     * Déclaration du controleur des gabarits d'affichage.
     *
     * @return void
     */
    public function registerPartial(): void
    {
        $this->getContainer()->share('wp.partial', function () {
            return new Partial($this->getContainer()->get(PartialContract::class), $this->getContainer());
        });
    }

    /**
     * Déclaration du controleur des types de contenu.
     *
     * @return void
     */
    public function registerPostType(): void
    {
        $this->getContainer()->share('wp.post-type', function () {
            return new PostType($this->getContainer()->get('post-type'));
        });
    }

    /**
     * Déclaration des controleurs de requête de récupération des éléments Wordpress.
     *
     * @return void
     */
    public function registerQuery(): void
    {
        $this->getContainer()->add('wp.query.post', function (?WP_Post $wp_post = null) {
            return !is_null($wp_post) ? QueryPost::create($wp_post) : QueryPost::createFromGlobal();
        });

        $this->getContainer()->add('wp.query.term', function (?WP_Term $wp_term) {
            return !is_null($wp_term) ? QueryTerm::create($wp_term) : QueryTerm::createFromGlobal();
        });

        $this->getContainer()->add('wp.query.user', function (?WP_User $wp_user = null) {
            return !is_null($wp_user) ? QueryUser::create($wp_user) : QueryUser::createFromGlobal();
        });
    }

    /**
     * Déclaration des controleurs de routage.
     *
     * @return void
     */
    public function registerRouting(): void
    {
        $this->getContainer()->share('wp.routing', function () {
            return new WpRouting($this->getContainer()->get(RouterInterface::class));
        });

        $this->getContainer()->share('wp.wp_query', function () {
            return new WpQuery();
        });

        $this->getContainer()->add('wp.wp_screen', function (?WP_Screen $wp_screen = null) {
            return new WpScreen($wp_screen);
        });
    }

    /**
     * Déclaration du gestionnaire de session.
     *
     * @return void
     */
    public function registerSession(): void
    {
        $this->getContainer()->share('wp.session', function () {
            return new WpSession($this->getContainer()->get(SessionManagerInterface::class));
        });
    }

    /**
     * Déclaration du controleur de taxonomie.
     *
     * @return void
     */
    public function registerTaxonomy(): void
    {
        $this->getContainer()->share('wp.taxonomy', function () {
            return new Taxonomy($this->getContainer()->get('taxonomy'));
        });
    }

    /**
     * Déclaration du controleur de gabarit.
     *
     * @return void
     */
    public function registerTemplate(): void
    {
        $this->getContainer()->share('wp.template', function () {
            return new Template($this->getContainer()->get('template'));
        });
    }

    /**
     * Déclaration du controleur utilisateur.
     *
     * @return void
     */
    public function registerUser(): void
    {
        $this->getContainer()->share('wp.user', function () {
            return new User($this->getContainer()->get('user'));
        });
    }

    /**
     * Déclaration du controleur de ganarit d'affichage.
     *
     * @return void
     */
    public function registerView(): void
    {
        $this->getContainer()->share('wp.view', function () {
            return new View($this->getContainer()->get('view'));
        });
    }
}