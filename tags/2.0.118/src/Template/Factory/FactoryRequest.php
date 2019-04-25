<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use tiFy\Contracts\Template\FactoryRequest as FactoryRequestContract;
use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Http\Request;

class FactoryRequest extends Request implements FactoryRequestContract
{
    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $factory;

    /**
     * Définition de l'instance du controleur de motif d'affichage.
     *
     * @param TemplateFactory $factory Instance du gabarit d'affichage associé.
     *
     * @return $this
     */
    public function setTemplateFactory(TemplateFactory $factory): FactoryRequestContract
    {
        $this->factory = $factory;

        return $this;
    }
}