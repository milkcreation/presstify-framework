<?php

namespace tiFy\Wp;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Contracts\Wp\WpScreenInterface;
use tiFy\Wp\WpQuery;
use tiFy\Wp\WpScreen;

class WpServiceProvider extends AppServiceProvider
{
    /**
     * Liste des alias de qualification de services.
     * @var array
     */
    protected $aliases = [
        WpScreenInterface::class => WpScreen::class
    ];

    /**
     * Liste des services à instance multiples auto-déclarés.
     * @var string[]
     */
    protected $bindings = [
        WpScreen::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('wp.query', function() {new WpQuery();})->build();
    }
}