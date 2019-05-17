<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use tiFy\Contracts\Template\FactoryLabels as FactoryLabelsContract;
use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Support\LabelsBag;

class FactoryLabels extends LabelsBag implements FactoryLabelsContract
{
    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $factory;

    /**
     * CONSTRUCTEUR.
     *
     * @param TemplateFactory $factory Instance du gabarit d'affichage associé.
     *
     * @return void
     */
    public function __construct(TemplateFactory $factory)
    {
        $this->factory = $factory;

        parent::__construct($this->factory->name(), $this->factory->get('labels', []));
    }
}