<?php

namespace tiFy\Template\Templates;

use tiFy\Contracts\Kernel\Request;
use tiFy\Contracts\Routing\Router;
use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Routing\Url;

class BaseUrl extends Url
{
    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $template;

    /**
     * CONSTRUCTEUR.
     *
     * @param Router $router
     * @param Request $request
     * @param TemplateFactory $template Instance du gabarit d'affichage associé.
     *
     * @return void
     */
    public function __construct(Router $router, Request $request, TemplateFactory $template)
    {
        $this->template = $template;

        parent::__construct($router, $request);
    }
}