<?php

namespace tiFy\Kernel;

/**
 * Application
 */
use App\App;

/**
 * Composants
 */
use tiFy\Cron\Cron;
use tiFy\Media\Media;
use tiFy\MetaTag\MetaTag;
use tiFy\PageHook\PageHook;
use tiFy\Route\Route;

use tiFy\Contracts\Views\ViewsInterface;

use tiFy\Kernel\Assets\Assets;
use tiFy\Kernel\Assets\AssetsInterface;
use tiFy\Kernel\ClassInfo\ClassInfo;
use tiFy\Kernel\Composer\ClassLoader;
use tiFy\Kernel\Events\Events;
use tiFy\Kernel\Http\Request;
use tiFy\Kernel\Logger\Logger;
use tiFy\Kernel\Templates\Engine;
use tiFy\Kernel\Service;

use tiFy\Kernel\Container\ServiceProvider;
use tiFy\tiFy;

class KernelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $singletons = [
        Assets::class,
        Partial::class,
        Events::class
    ];

    /**
     * {@inheritdoc}
     */
    protected $bindings = [
        ClassInfo::class,
        Engine::class
    ];

    /**
     * {@inheritdoc}
     */
    protected $aliases = [
        ViewsInterface::class => Engine::class
    ];

    /**
     * Liste des packages natifs (composants)
     * @return array
     */
    protected $components = [
        Cron::class,
        Media::class,
        MetaTag::class,
        PageHook::class,
        Route::class
    ];

    /**
     * Liste des packages additionnels (extensions)
     * @return array
     */
    protected $plugins = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $app = $this->getContainer()->singleton(App::class)->build();

        foreach ($this->getBootables() as $bootable) :
            $class = $this->getContainer()->resolve($bootable, [$app]);
        endforeach;

        $this->getContainer()->singleton(
            'tiFyLogger',
            function () {
                return Logger::globalReport();
            }
        );
        $this->getContainer()->singleton(
            'tiFyRequest',
            function () {
                return Request::capture();
            }
        );

        do_action('after_setup_tify');
    }

    /**
     * Récupération de la liste des services lancés au démarrage.
     *
     * @return array
     */
    public function getBootables()
    {
        return array_merge(
            [
                Assets::class
            ],
            $this->components,
            $this->plugins
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return tiFy
     */
    public function getContainer()
    {
        return parent::getContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        foreach($this->components as $component) :
            array_push($this->singletons, $component);
        endforeach;

        /** @todo Modifier le chargement des plugins */
        if (!defined('TIFY_CONFIG_DIR')) :
            define('TIFY_CONFIG_DIR', get_template_directory() . '/config');
        endif;

        if (file_exists(TIFY_CONFIG_DIR . '/plugins.php')) :
            $plugins = include TIFY_CONFIG_DIR . '/plugins.php';

            foreach (array_keys($plugins) as $plugin) :
                array_push($this->plugins, $plugin);
                array_push($this->singletons, $plugin);
            endforeach;
        endif;

        parent::parse();
    }
}