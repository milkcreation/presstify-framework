<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use tiFy\Contracts\Template\FactoryUrl as FactoryUrlContract;
use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Routing\Url;

class FactoryUrl extends Url implements FactoryUrlContract
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

        parent::__construct(router(), request());
    }
}