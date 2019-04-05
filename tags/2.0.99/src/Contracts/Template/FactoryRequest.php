<?php declare(strict_types=1);

namespace tiFy\Contracts\Template;

use tiFy\Contracts\Http\Request;

interface FactoryRequest extends Request
{
    /**
     * Définition de l'instance du controleur de motif d'affichage.
     *
     * @param TemplateFactory $factory Instance du gabarit d'affichage associé.
     *
     * @return static
     */
    public function setTemplateFactory(TemplateFactory $factory): FactoryRequest;
}