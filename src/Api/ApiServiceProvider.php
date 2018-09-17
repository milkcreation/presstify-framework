<?php

/**
 * @name API
 * @package PresstiFy
 * @subpackage Components
 * @namespace tiFy\Api
 * @desc Gestion d'API
 * @author Jordy Manner
 * @copyright Tigre Blanc Digital
 * @version 1.2.369
 */

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
     * Liste des services à instance unique auto-déclarés.
     * @var string[]
     */
    protected $singletons = [
        Api::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->resolve(Api::class, [$this->app]);

        $this->app->singleton(
            Facebook::class,
            function ($app) {
                return Facebook::create(\config('api.facebook', []));
            }
        );
        $this->app->singleton(
            Recaptcha::class,
            function ($app) {
                return Recaptcha::create(\config('api.recaptcha', []));
            }
        );

        $this->app->singleton(
            Youtube::class,
            function ($app) {
                return Youtube::make(\config('api.youtube', []));
            }
        );
    }
}