<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use App\App;
use Pollen\Asset\AssetManagerInterface;
use Pollen\Cookie\CookieJarInterface;
use Pollen\Database\DatabaseManagerInterface;
use Pollen\Debug\DebugManagerInterface;
use Pollen\Field\FieldManagerInterface;
use Pollen\Filesystem\StorageManagerInterface;
use Pollen\Form\FormManagerInterface;
use Pollen\Http\RequestInterface;
use Pollen\Kernel\ApplicationInterface;
use Pollen\Kernel\Kernel;
use Pollen\Mail\MailManagerInterface;
use Pollen\Metabox\MetaboxManagerInterface;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Routing\RouterInterface;
use Pollen\Session\SessionManagerInterface;
use Pollen\Support\DateTime;
use RuntimeException;
use tiFy\Support\Locale;

class WpKernel extends Kernel
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if (!$this->isBooted()) {
            if (defined('WP_INSTALLING') && (WP_INSTALLING === true)) {
                return;
            }

            parent::boot();
        }
    }

    /**
     * Chargement de l'application.
     *
     * @return void
     */
    protected function bootApp(): void
    {
        $this->app = class_exists(App::class) ? new App() : new WpApplication();

        if (!$this->app instanceof WpApplicationInterface) {
            throw new RuntimeException(sprintf('Application must be an instance of %s', WpApplicationInterface::class));
        }
    }

    /**
     * @implements
     */
    protected function bootServices(): void
    {
        if (!defined('ABSPATH')) {
            throw new RuntimeException('Wordpress must be installed to work.');
        }

        require_once __DIR__ . '/helpers.php';

        if (file_exists(ABSPATH . 'wp-admin/includes/translation-install.php')) {
            require_once(ABSPATH . 'wp-admin/includes/translation-install.php');
        }

        Locale::set(get_locale());
        Locale::setLanguages(wp_get_available_translations() ?: []);

        /**  * /
        add_action('plugins_loaded', function () {
                load_muplugin_textdomain('tify', '/presstify/languages/');
                do_action('tify_load_textdomain');
            }
        );
        /**/

        global $locale;
        DateTime::setLocale($locale);

        if ($this->getApp()->has(DebugManagerInterface::class)) {
            $this->getApp()->get('wp.debug');
        }

        if ($this->getApp()->has(RouterInterface::class)) {
            $this->getApp()->get('wp.routing');
        }

        if ($this->getApp()->has(AssetManagerInterface::class)) {
            $this->getApp()->get('wp.asset');
        }

        $this->getApp()->get('wp.column');

        if ($this->getApp()->has(CookieJarInterface::class)) {
            $this->getApp()->get('wp.cookie');
        }

        if ($this->getApp()->has('cron')) {
            $this->getApp()->get('cron');
        }

        if ($this->getApp()->has(DatabaseManagerInterface::class)) {
            $this->getApp()->get('wp.database');
        }

        if ($this->getApp()->has(FieldManagerInterface::class)) {
            $this->getApp()->get('wp.field');
        }

        if ($this->getApp()->has(FormManagerInterface::class)) {
            $this->getApp()->get('wp.form');
        }

        if ($this->getApp()->has(RequestInterface::class)) {
            $this->getApp()->get('wp.http.request');
        }

        if ($this->getApp()->has(MailManagerInterface::class)) {
            $this->getApp()->get('wp.mail');
        }

        $this->getApp()->get('wp.media');

        if ($this->getApp()->has(MetaboxManagerInterface::class)) {
            $this->getApp()->get('wp.metabox');
        }

        $this->getApp()->get('wp.option');

        if ($this->getApp()->has(PartialManagerInterface::class)) {
            $this->getApp()->get('wp.partial');
        }

        if ($this->getApp()->has(SessionManagerInterface::class)) {
            $this->getApp()->get('wp.session');
        }

        if ($this->getApp()->has(StorageManagerInterface::class)) {
            $this->getApp()->get('wp.filesystem');
        }

        if ($this->getApp()->has('view')) {
            $this->getApp()->get('wp.view');
        }

        //events()->trigger('wp.booted');

        parent::bootServices();
    }

    /**
     * {@inheritDoc}
     *
     * @return WpApplicationInterface
     */
    public function getApp(): ApplicationInterface
    {
        return parent::getApp();
    }
}
