<?php

namespace tiFy\Template\Templates;

use tiFy\Contracts\Template\TemplateFactory;

class BaseAssets
{
    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $template;

    /**
     * CONSTRUCTEUR.
     *
     * @param TemplateFactory $template Instance du gabarit d'affichage associé.
     *
     * @return void
     */
    public function __construct(TemplateFactory $template)
    {
        $this->template = $template;
    }
}