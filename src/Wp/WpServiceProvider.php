<?php

namespace tiFy\Wp;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Wp\WpScreen;

class WpServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $bindings = [
        WpScreen::class
    ];
}