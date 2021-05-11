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
use Pollen\Metabox\MetaboxManagerInterface;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Routing\RouterInterface;
use Pollen\Session\SessionManagerInterface;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\DateTime;
use Pollen\Support\Proxy\HttpRequestProxy;
use RuntimeException;
use Pollen\Container\BootableServiceProvider;
use tiFy\Support\Locale;
use tiFy\Wordpress\Column\Column;
use tiFy\Wordpress\Media\Media;
use tiFy\Wordpress\Option\Option;
use tiFy\Wordpress\View\View;

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
        'wp.partial',
        'wp.option',
        'wp.routing',
        'wp.session',
        'wp.wp_query',
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

                    $this->getContainer()->get('wp.column');

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

                    if ($this->getContainer()->has(MetaboxManagerInterface::class)) {
                        $this->getContainer()->get('wp.metabox');
                    }

                    $this->getContainer()->get('wp.option');

                    if ($this->getContainer()->has(PartialManagerInterface::class)) {
                        $this->getContainer()->get('wp.partial');
                    }

                    if ($this->getContainer()->has(SessionManagerInterface::class)) {
                        $this->getContainer()->get('wp.session');
                    }

                    if ($this->getContainer()->has(StorageManagerInterface::class)) {
                        $this->getContainer()->get('wp.filesystem');
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
        $this->registerPartial();
        $this->registerRouting();
        $this->registerSession();
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
                return new Column();
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
                return new WpMetabox($this->getContainer()->get(MetaboxManagerInterface::class), $this->getContainer());
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