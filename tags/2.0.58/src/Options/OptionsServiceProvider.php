<?php

namespace tiFy\Options;

use tiFy\Contracts\Options\OptionsPageInterface;
use tiFy\Options\Options;
use tiFy\Options\Page\OptionsPageController;
use tiFy\App\Container\AppServiceProvider;

class OptionsServiceProvider extends AppServiceProvider
{
    /**
     * Liste des alias de qualification de services.
     * @var array
     */
    protected $aliases = [
        OptionsPageInterface::class => OptionsPageController::class
    ];

    /**
     * Liste des services à instance multiples auto-déclarés.
     * @var string[]
     */
    protected $bindings = [
        OptionsPageController::class
    ];

    /**
     * Liste des services à instance unique auto-déclarés.
     * @var string[]
     */
    protected $singletons = [
        Options::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->resolve(Options::class, [$this->app]);
    }
}