<?php

namespace tiFy\Kernel;

use tiFy\Kernel\Assets\Assets;
use tiFy\Kernel\ClassInfo\ClassInfo;
use tiFy\Container\ServiceProvider;
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

class KernelServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'assets',
        ClassInfo::class,
        'encrypter',
        'events',
        'events.listener',
        'logger',
        'notices',
        'params.bag',
        'request',
        'redirect',
        'validator',
        'view.engine',
        'view.engine'
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        if (!defined('TIFY_CONFIG_DIR')) :
            define('TIFY_CONFIG_DIR', get_template_directory() . '/config');
        endif;
    }

    /**
     * @inheritdoc
     */
    public function boot()
    {

    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('assets', function () {
            return new Assets();
        });

        $this->getContainer()->add(ClassInfo::class);

        $this->getContainer()->add('encrypter', function ($secret = null, $private = null) {
            return new Encrypter($secret, $private);
        });

        $this->getContainer()->share('events', function () {
            return new EventsManager();
        });

        $this->getContainer()->add('events.listener', function (callable $callback) {
            return new Listener($callback);
        });

        $this->getContainer()->add('logger', function ($name = null, $attrs = []) {
            return Logger::create($name, $attrs);
        });

        $this->getContainer()->add('notices', function () {
            return new Notices();
        });

        $this->getContainer()->add('params.bag', function ($attrs = []) {
            return new ParamsBag($attrs);
        });

        $this->getContainer()->share('request', function () {
            return Request::capture();
        });

        $this->getContainer()->add('redirect', function (?string $url, int $status = null, array $headers = []) {
            return new RedirectResponse($url, $status, $headers);
        });

        $this->getContainer()->add('validator', function () {
            return new Validator();
        });

        $this->getContainer()->add('view.engine', function () {
            return new ViewEngine();
        });
    }
}