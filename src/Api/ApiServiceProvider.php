<?php

namespace tiFy\Api;

use tiFy\Api\Api;
use tiFy\Api\Facebook\Facebook;
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
        );

        $this->app->singleton(
            Facebook::class,
            function ($app) {
                return Facebook::create(config('api.facebook', []));
            }
        );

        $this->app->singleton(
            Recaptcha::class,
            function ($app) {
                return Recaptcha::create(config('api.recaptcha', []));
            }
        );

        $this->app->singleton(
            Youtube::class,
            function ($app) {
                return Youtube::make(config('api.youtube', []));
            }
        );
    }
}