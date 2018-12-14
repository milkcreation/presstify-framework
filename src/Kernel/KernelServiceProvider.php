<?php

namespace tiFy\Kernel;

/**
 * Application
 */

use App\App;

/**
 * Composants
 */

use tiFy\PageHook\PageHook;
use tiFy\Kernel\Assets\Assets;
use tiFy\Kernel\ClassInfo\ClassInfo;
use tiFy\Kernel\Container\ServiceProvider;
use tiFy\Kernel\Encryption\Encrypter;
use tiFy\Kernel\Events\Manager as EventsManager;
use tiFy\Kernel\Events\Listener;
use tiFy\Kernel\Http\RedirectResponse;
use tiFy\Kernel\Http\Request;
use tiFy\Kernel\Logger\Logger;
use tiFy\Kernel\Notices\Notices;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Kernel\Validation\Validator;
use tiFy\View\ViewEngine;
use tiFy\tiFy;

class KernelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $bindings = [
        ClassInfo::class,
    ];

    /**
     * Liste des packages natifs (composants)
     * @return array
     */
    protected $components = [
        PageHook::class,
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
        $this->getContainer()->singleton('events', function () {
            return new EventsManager();
        });

        $this->getContainer()->bind(
            'events.listener',
            function (callable $callback) {
                return new Listener($callback);
            }
        );

        $this->getContainer()->bind('notices', function () {
            return new Notices();
        });

        $this->getContainer()->bind('params.bag', function ($attrs = []) {
            return new ParamsBag($attrs);
        });

        $this->getContainer()->singleton('request', function () {
            return Request::capture();
        });

        $this->getContainer()->bind('redirect', function (?string $url, int $status = 302, array $headers = []) {
            return new RedirectResponse($url, $status, $headers);
        });

        $this->getContainer()->bind('validator', function () {
            return new Validator();
        });

        $this->getContainer()->bind('view.engine', function () {
            return new ViewEngine();
        });

        $app = $this->getContainer()->singleton(App::class)->build();

        $this->getContainer()->singleton('assets', function () {
            return new Assets();
        })->build();

        $this->getContainer()->bind(
            'encrypter',
            function ($secret = null, $private = null) {
                return new Encrypter($secret, $private);
            }
        );

        $this->getContainer()->bind(
            'logger',
            function ($name = null, $attrs = []) use ($app) {
                return Logger::create($name, $attrs, $app);
            }
        );

        foreach ($this->getBootables() as $bootable) :
            $this->getContainer()->resolve($bootable, [$app]);
        endforeach;

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
        foreach ($this->components as $component) :
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