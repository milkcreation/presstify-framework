<?php

namespace tiFy\Template\Templates;

use tiFy\Kernel\Labels\LabelsBag;
use tiFy\Contracts\Template\TemplateFactory;

class BaseLabels extends LabelsBag
{
    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $template;

    /**
     * CONSTRUCTEUR.
     *
     * @param string Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param TemplateFactory $template Instance du gabarit d'affichage associé.
     *
     * @return void
     */
    public function __construct($name, $attrs, TemplateFactory $template)
    {
        $this->template = $template;

        parent::__construct($name, $attrs);
    }
}