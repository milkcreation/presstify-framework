<?php

namespace tiFy\Template\Templates;

use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Kernel\Http\Request;

class BaseRequest extends Request
{
    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $template;

    /**
     * Définition de l'instance du controleur de motif d'affichage.
     *
     * @param TemplateFactory $template Instance du gabarit d'affichage associé.
     *
     * @return $this
     */
    public function setTemplate(TemplateFactory $template)
    {
        $this->template = $template;

        return $this;
    }
}