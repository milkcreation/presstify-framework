<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use tiFy\Contracts\Template\FactoryNotices as FactoryNoticesContract;
use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Kernel\Notices\Notices;

class FactoryNotices extends Notices implements FactoryNoticesContract
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
    }
}