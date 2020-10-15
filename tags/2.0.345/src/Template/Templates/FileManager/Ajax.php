<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager;

use tiFy\Template\Factory\{Ajax as BaseAjax, FactoryAwareTrait};
use tiFy\Template\Templates\FileManager\Contracts\{Ajax as AjaxContract, Factory};

class Ajax extends BaseAjax implements AjaxContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit associé.
     * @var Factory
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function parse()
    {
        parent::parse();

        $this->getFactory()->param()->set('attrs.data-options', $this->all());

        return $this;
    }
}