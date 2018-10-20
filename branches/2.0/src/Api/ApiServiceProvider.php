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
        $this->app->singleton('api', function () { return new Api(); })->build();

        if (config('api.facebook', [])) :
            $this->app->singleton('api.facebook',
                function ($args = []) {
                    return Facebook::create($args);
                }
            )->build([config('api.facebook', [])]);

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
        endif;

        if (config('api.recaptcha', [])) :
            $this->app->singleton(
                'api.recaptcha',
                function ($args = []) {
                    return Recaptcha::create($args);
                }
            )->build([config('api.recaptcha', [])]);
        endif;

        if (config('api.youtube', [])) :
            $this->app->singleton(
                'api.youtube',
                function ($args = []) {
                    return Youtube::make($args);
                }
            )->build([config('api.youtube', [])]);
        endif;
    }
}