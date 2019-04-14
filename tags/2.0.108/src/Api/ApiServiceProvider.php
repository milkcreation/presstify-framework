<?php

namespace tiFy\Api;

use tiFy\Api\Facebook\Facebook;
use tiFy\Api\Facebook\FacebookProfileController;
use tiFy\Api\Facebook\FacebookSigninController;
use tiFy\Api\Facebook\FacebookSignupController;
use tiFy\Api\Recaptcha\Recaptcha;
use tiFy\Api\Youtube\Youtube;
use tiFy\Container\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'api',
        'api.facebook',
        'api.facebook.profile',
        'api.facebook.signin',
        'api.facebook.signup',
        'api.recaptcha',
        'api.youtube',
    ];

    /**
     * @inheritdoc
     */
    public function boot()
    {
        add_action('after_setup_theme', function () {
            $this->getContainer()->get('api');
        });
    }

    public function register()
    {
        $this->getContainer()->share('api', function () {
            return new Api($this->getContainer());
        });

        $this->getContainer()->share('api.facebook', function () {
            return Facebook::create(config('api.facebook', []));
        });

        $this->getContainer()->share('api.facebook.profile', function () {
            $concrete = config('api.facebook.profile', FacebookProfileController::class);
            return new $concrete();
        });

        $this->getContainer()->share('api.facebook.signin', function () {
            $concrete = config('api.facebook.signin', FacebookSigninController::class);
            return new $concrete();
        });

        $this->getContainer()->share('api.facebook.signup', function () {
            $concrete = config('api.facebook.signup', FacebookSignupController::class);
            return new $concrete();
        });

        $this->getContainer()->share('api.recaptcha', function () {
            return Recaptcha::create(config('api.recaptcha', []));
        });

        $this->getContainer()->share('api.youtube', function () {
            return Youtube::create(config('api.youtube', []));
        });
    }
}