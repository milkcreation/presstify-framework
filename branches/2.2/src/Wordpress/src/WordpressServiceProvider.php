<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Asset\AssetManagerInterface;
use Pollen\Database\DatabaseManagerInterface;
use Pollen\Debug\DebugManagerInterface;
use Pollen\Cookie\CookieJarInterface;
use Pollen\Field\FieldManagerInterface;
use Pollen\Filesystem\StorageManagerInterface;
use Pollen\Form\FormManagerInterface;
use Pollen\Http\RequestInterface;
use Pollen\Mail\MailManagerInterface;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Routing\RouterInterface;
use Pollen\Session\SessionManagerInterface;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\DateTime;
use Pollen\Support\Proxy\HttpRequestProxy;
use RuntimeException;
use Pollen\Container\BootableServiceProvider;
use tiFy\Metabox\Contracts\MetaboxContract;
use tiFy\Support\Locale;
use tiFy\Wordpress\Column\Column;
use tiFy\Wordpress\Media\Media;
use tiFy\Wordpress\Metabox\Metabox;
use tiFy\Wordpress\Option\Option;
use tiFy\Wordpress\PageHook\PageHook;
use tiFy\Wordpress\PageHook\PageHookMetabox;
use tiFy\Wordpress\PostType\PostType;
use tiFy\Wordpress\Query\QueryPost;
use tiFy\Wordpress\Query\QueryTerm;
use tiFy\Wordpress\Query\QueryUser;
use tiFy\Wordpress\Taxonomy\Taxonomy;
use tiFy\Wordpress\User\User;
use tiFy\Wordpress\User\Role\RoleFactory;
use tiFy\Wordpress\View\View;
use WP_Post;
use WP_Screen;
use WP_Term;
use WP_User;

class WordpressServiceProvider extends BootableServiceProvider
{
    use BootableTrait;
    use HttpRequestProxy;

    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        'wp.asset',
        'wp.column',
        'wp.cookie',
        'wp.database',
        'wp.db',
        'wp.debug',
        'wp.field',
        'wp.filesystem',
        'wp.form',
        'wp.http.request',
        'wp.login-redirect',
        'wp.mail',
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
        if (!$this->isBooted()) {
            if (!defined('WPINC')) {
                throw new RuntimeException('Wordpress must be installed to work');
            }

            require_once __DIR__ . '/helpers.php';

            $this->getContainer()->share('wp', new Wordpress());

            add_action(
                'plugins_loaded',
                function () {
                    load_muplugin_textdomain('tify', '/presstify/languages/');
                    do_action('tify_load_textdomain');
                }
            );

            add_action(
                'after_setup_theme',
                function () {
                    require_once(ABSPATH . 'wp-admin/includes/translation-install.php');

                    Locale::set(get_locale());
                    Locale::setLanguages(wp_get_available_translations() ?: []);

                    global $locale;
                    DateTime::setLocale($locale);

                    if ($this->getContainer()->has(DebugManagerInterface::class)) {
                        $this->getContainer()->get('wp.debug');
                    }

                    if ($this->getContainer()->has(RouterInterface::class)) {
                        $this->getContainer()->get('wp.routing');
                    }

                    if ($this->getContainer()->has(AssetManagerInterface::class)) {
                        $this->getContainer()->get('wp.asset');
                    }

                    if ($this->getContainer()->has('column')) {
                        $this->getContainer()->get('wp.column');
                    }

                    if ($this->getContainer()->has(CookieJarInterface::class)) {
                        $this->getContainer()->get('wp.cookie');
                    }

                    if ($this->getContainer()->has('cron')) {
                        $this->getContainer()->get('cron');
                    }

                    if ($this->getContainer()->has(DatabaseManagerInterface::class)) {
                        $this->getContainer()->get('wp.database');
                    }

                    if ($this->getContainer()->has(FieldManagerInterface::class)) {
                        $this->getContainer()->get('wp.field');
                    }

                    if ($this->getContainer()->has(FormManagerInterface::class)) {
                        $this->getContainer()->get('wp.form');
                    }

                    if ($this->getContainer()->has(RequestInterface::class)) {
                        $this->getContainer()->get('wp.http.request');
                    }

                    if ($this->getContainer()->has(MailManagerInterface::class)) {
                        $this->getContainer()->get('wp.mail');
                    }

                    $this->getContainer()->get('wp.media');

                    if ($this->getContainer()->has(MetaboxContract::class)) {
                        $this->getContainer()->get('wp.metabox');
                    }

                    $this->getContainer()->get('wp.page-hook');

                    $this->getContainer()->get('wp.option');

                    if ($this->getContainer()->has(PartialManagerInterface::class)) {
                        $this->getContainer()->get('wp.partial');
                    }

                    if ($this->getContainer()->has('post-type')) {
                        $this->getContainer()->get('wp.post-type');
                    }

                    if ($this->getContainer()->has(SessionManagerInterface::class)) {
                        $this->getContainer()->get('wp.session');
                    }

                    if ($this->getContainer()->has(StorageManagerInterface::class)) {
                        $this->getContainer()->get('wp.filesystem');
                    }

                    if ($this->getContainer()->has('taxonomy')) {
                        $this->getContainer()->get('wp.taxonomy');
                    }

                    if ($this->getContainer()->has('user')) {
                        $this->getContainer()->get('wp.user');
                        $this->getContainer()->add(
                            'user.role.factory',
                            function () {
                                return new RoleFactory();
                            }
                        );
                    }

                    if ($this->getContainer()->has('view')) {
                        $this->getContainer()->get('wp.view');
                    }

                    events()->trigger('wp.booted');
                },
                1
            );

            $this->setBooted();
        }
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->registerAsset();
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
        $this->getContainer()->share(
            'wp.asset',
            function () {
                return new WpAsset($this->getContainer()->get(AssetManagerInterface::class), $this->getContainer());
            }
        );
    }

    /**
     * Déclaration du controleur des colonnes.
     *
     * @return void
     */
    public function registerColumn(): void
    {
        $this->getContainer()->share(
            'wp.column',
            function () {
                return new Column($this->getContainer()->get('column'));
            }
        );
    }

    /**
     * Déclaration du controleur des cookies.
     *
     * @return void
     */
    public function registerCookie(): void
    {
        $this->getContainer()->share(
            'wp.cookie',
            function () {
                return new WpCookie($this->getContainer()->get(CookieJarInterface::class), $this->getContainer());
            }
        );
    }

    /**
     * Déclaration du controleur de base de données.
     *
     * @return void
     */
    public function registerDatabase(): void
    {
        $this->getContainer()->share(
            'wp.database',
            function () {
                return new WpDatabase(
                    $this->getContainer()->get(DatabaseManagerInterface::class), $this->getContainer()
                );
            }
        );
    }

    /**
     * Déclaration du gestionnaire de deboguage.
     *
     * @return void
     */
    public function registerDebug(): void
    {
        $this->getContainer()->share(
            'wp.debug',
            function () {
                return new WpDebug($this->getContainer()->get(DebugManagerInterface::class), $this->getContainer());
            }
        );
    }

    /**
     * Déclaration du controleur de système de fichiers.
     *
     * @return void
     */
    public function registerFilesystem(): void
    {
        $this->getContainer()->share(
            'wp.filesystem',
            function () {
                return new WpFilesystem(
                    $this->getContainer()->get(StorageManagerInterface::class), $this->getContainer()
                );
            }
        );
    }

    /**
     * Déclaration du gestionnaire de champs.
     *
     * @return void
     */
    public function registerField(): void
    {
        $this->getContainer()->share(
            'wp.field',
            function () {
                return new WpField($this->getContainer()->get(FieldManagerInterface::class), $this->getContainer());
            }
        );
    }

    /**
     * Déclaration du controleur des formulaires.
     *
     * @return void
     */
    public function registerForm(): void
    {
        $this->getContainer()->share(
            'wp.form',
            function () {
                return new WpForm($this->getContainer()->get(FormManagerInterface::class), $this->getContainer());
            }
        );
    }

    /**
     * Déclaration du controleur des processus HTTP. Requête, Réponse, Session ...
     *
     * @return void
     */
    public function registerHttp(): void
    {
        $this->getContainer()->share(
            'wp.http.request',
            function () {
                return new WpHttpRequest($this->getContainer()->get(RequestInterface::class), $this->getContainer());
            }
        );
    }

    /**
     * Déclaration du gestionnaire de mail.
     *
     * @return void
     */
    public function registerMailer(): void
    {
        $this->getContainer()->share(
            'wp.mail',
            function () {
                return new WpMail($this->getContainer()->get(MailManagerInterface::class), $this->getContainer());
            }
        );
    }

    /**
     * Déclaration du controleur de gestion des Medias.
     *
     * @return void
     */
    public function registerMedia(): void
    {
        $this->getContainer()->share(
            'wp.media',
            function () {
                return new Media();
            }
        );
    }

    /**
     * Déclaration du controleur de gestion de metaboxes.
     *
     * @return void
     */
    public function registerMetabox(): void
    {
        $this->getContainer()->share(
            'wp.metabox',
            function () {
                return new Metabox($this->getContainer()->get(MetaboxContract::class), $this->getContainer());
            }
        );
    }

    /**
     * Déclaration du controleur des options
     *
     * @return void
     */
    public function registerOptions(): void
    {
        $this->getContainer()->share(
            'wp.option',
            function () {
                return new Option();
            }
        );
    }

    /**
     * Déclaration du controleur des pages d'accroche.
     *
     * @return void
     */
    public function registerPageHook(): void
    {
        $this->getContainer()->share(
            'wp.page-hook',
            function () {
                return new PageHook();
            }
        );
        $this->getContainer()->add(
            PageHookMetabox::class,
            function () {
                return new PageHookMetabox(
                    $this->getContainer()->get('wp.page-hook'),
                    $this->getContainer()->get(MetaboxContract::class)
                );
            }
        );
    }

    /**
     * Déclaration du controleur des gabarits d'affichage.
     *
     * @return void
     */
    public function registerPartial(): void
    {
        $this->getContainer()->share(
            'wp.partial',
            function () {
                return new WpPartial($this->getContainer()->get(PartialManagerInterface::class), $this->getContainer());
            }
        );
    }

    /**
     * Déclaration du controleur des types de contenu.
     *
     * @return void
     */
    public function registerPostType(): void
    {
        $this->getContainer()->share(
            'wp.post-type',
            function () {
                return new PostType($this->getContainer()->get('post-type'));
            }
        );
    }

    /**
     * Déclaration des controleurs de requête de récupération des éléments Wordpress.
     *
     * @return void
     */
    public function registerQuery(): void
    {
        $this->getContainer()->add(
            'wp.query.post',
            function (?WP_Post $wp_post = null) {
                return !is_null($wp_post) ? QueryPost::create($wp_post) : QueryPost::createFromGlobal();
            }
        );

        $this->getContainer()->add(
            'wp.query.term',
            function (?WP_Term $wp_term) {
                return !is_null($wp_term) ? QueryTerm::create($wp_term) : QueryTerm::createFromGlobal();
            }
        );

        $this->getContainer()->add(
            'wp.query.user',
            function (?WP_User $wp_user = null) {
                return !is_null($wp_user) ? QueryUser::create($wp_user) : QueryUser::createFromGlobal();
            }
        );
    }

    /**
     * Déclaration des controleurs de routage.
     *
     * @return void
     */
    public function registerRouting(): void
    {
        $this->getContainer()->share(
            'wp.routing',
            function () {
                return new WpRouting($this->getContainer()->get(RouterInterface::class), $this->getContainer());
            }
        );

        $this->getContainer()->share(
            'wp.wp_query',
            function () {
                return new WpQuery();
            }
        );

        $this->getContainer()->add(
            'wp.wp_screen',
            function (?WP_Screen $wp_screen = null) {
                return new WpScreen($wp_screen);
            }
        );
    }

    /**
     * Déclaration du gestionnaire de session.
     *
     * @return void
     */
    public function registerSession(): void
    {
        $this->getContainer()->share(
            'wp.session',
            function () {
                return new WpSession($this->getContainer()->get(SessionManagerInterface::class), $this->getContainer());
            }
        );
    }

    /**
     * Déclaration du controleur de taxonomie.
     *
     * @return void
     */
    public function registerTaxonomy(): void
    {
        $this->getContainer()->share(
            'wp.taxonomy',
            function () {
                return new Taxonomy($this->getContainer()->get('taxonomy'));
            }
        );
    }

    /**
     * Déclaration du controleur utilisateur.
     *
     * @return void
     */
    public function registerUser(): void
    {
        $this->getContainer()->share(
            'wp.user',
            function () {
                return new User($this->getContainer()->get('user'));
            }
        );
    }

    /**
     * Déclaration du controleur de ganarit d'affichage.
     *
     * @return void
     */
    public function registerView(): void
    {
        $this->getContainer()->share(
            'wp.view',
            function () {
                return new View($this->getContainer()->get('view'));
            }
        );
    }
}