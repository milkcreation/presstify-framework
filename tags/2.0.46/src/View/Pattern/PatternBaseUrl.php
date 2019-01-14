<?php

namespace tiFy\View\Pattern;

use tiFy\Contracts\Kernel\Request;
use tiFy\Contracts\Routing\Router;
use tiFy\Contracts\View\ViewPatternController;
use tiFy\Routing\Url;

class PatternBaseUrl extends Url
{
    /**
     * Instance de la disposition.
     * @var ViewPatternController
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param Router $router
     * @param Request $request
     * @param ViewPatternController $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct(Router $router, Request $request, ViewPatternController $pattern)
    {
        $this->pattern = $pattern;

        parent::__construct($router, $request);
    }
}