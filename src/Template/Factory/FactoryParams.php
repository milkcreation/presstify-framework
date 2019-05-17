<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use tiFy\Contracts\Template\FactoryParams as FactoryParamsContract;
use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Support\ParamsBag;

class FactoryParams extends ParamsBag implements FactoryParamsContract
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

        $this->set($this->factory->get('params', []))->parse();
    }

    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [
            'singular' => $this->factory->label('singular') ?: $this->factory->name(),
            'plural'   => $this->factory->label('plural') ?: $this->factory->name(),
        ];
    }
}