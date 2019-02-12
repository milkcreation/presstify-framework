<?php

namespace tiFy\Kernel;

/**
 * Application
 */

use App\App;

/**
 * Composants
 */

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
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->getContainer()->singleton('events', function () {
            return new EventsManager();
        });

        $this->getContainer()->bind('events.listener', function (callable $callback) {
            return new Listener($callback);
        });

        $this->getContainer()->bind('notices', function () {
            return new Notices();
        });

        $this->getContainer()->bind('params.bag', function ($attrs = []) {
            return new ParamsBag($attrs);
        });

        $this->getContainer()->singleton('request', function () {
            return Request::capture();
        });

        $this->getContainer()->bind('redirect', function (?string $url, int $status = null, array $headers = []) {
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

        $this->getContainer()->bind('encrypter', function ($secret = null, $private = null) {
            return new Encrypter($secret, $private);
        });

        $this->getContainer()->bind('logger', function ($name = null, $attrs = []) use ($app) {
            return Logger::create($name, $attrs, $app);
        });

        do_action('after_setup_tify');
    }

    /**
     * {@inheritdoc}
     *
     * @return tiFy|\League\Container\ContainerInterface
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
        /** @todo Modifier le chargement des plugins */
        if (!defined('TIFY_CONFIG_DIR')) :
            define('TIFY_CONFIG_DIR', get_template_directory() . '/config');
        endif;

        parent::parse();
    }
}