<?php

namespace tiFy\Template\Templates;

use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Kernel\Params\ParamsBag;

class BaseParams extends ParamsBag
{
    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $template;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration.
     * @param TemplateFactory $template Instance du gabarit d'affichage associé.
     *
     * @return void
     */
    public function __construct($attrs, TemplateFactory $template)
    {
        $this->template = $template;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'singular' => $this->template->label('singular') ? : $this->template->name(),
            'plural'   => $this->template->label('plural') ? : $this->template->name(),
        ];
    }
}