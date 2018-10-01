<?php

namespace tiFy\Api;

use tiFy\Api\Api;
use tiFy\Api\Facebook\Facebook;
use tiFy\Api\Facebook\FacebookProfileController;
use tiFy\Api\Facebook\FacebookSigninController;
use tiFy\Api\Facebook\FacebookSignupController;
use tiFy\Api\GoogleMap\GoogleMap;
use tiFy\Api\Recaptcha\Recaptcha;
use tiFy\Api\Vimeo\Vimeo;
use tiFy\Api\Youtube\Youtube;
use tiFy\App\Container\AppServiceProvider;

class ApiServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton(
            Api::class,
            function () {
                return new Api();
            }
        )->build();

        $this->app->singleton(
            Facebook::class,
            function ($app) {
                return Facebook::create(config('api.facebook', []));
            }
        )->build();

        $this->app->singleton(
            'api.facebook.profile',
            config('api.facebook.profile', FacebookProfileController::class)
        )->build();

        $this->app->singleton(
            'api.facebook.signin',
            config('api.facebook.signin', FacebookSigninController::class)
        )->build();

        $this->app->singleton(
            'api.facebook.signup',
            config('api.facebook.signup', FacebookSignupController::class)
        )->build();

        $this->app->singleton(
            Recaptcha::class,
            function ($app) {
                return Recaptcha::create(config('api.recaptcha', []));
            }
        )->build();

        $this->app->singleton(
            Youtube::class,
            function ($app) {
                return Youtube::make(config('api.youtube', []));
            }
        )->build();
    }
}