<?php

namespace tiFy\Routing;

use League\Uri\Components\Query;
use tiFy\Contracts\Kernel\Request;
use tiFy\Contracts\Routing\Router;

class Url
{
    /**
     * Instance du controleur de requête HTTP.
     * @var Router
     */
    protected $request;

    /**
     * Instance du controleur de routage.
     * @var Router
     */
    protected $router;

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct(Router $router, Request $request)
    {
        $this->router = $router;
        $this->request = $request;
    }
}