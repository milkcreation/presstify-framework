<?php

declare(strict_types=1);

namespace tiFy\Template\Factory;

use Pollen\Http\Request as BaseRequest;
use tiFy\Contracts\Template\FactoryRequest as FactoryRequestContract;
use tiFy\Contracts\Template\TemplateFactory;

class Request extends BaseRequest implements FactoryRequestContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $factory;
}