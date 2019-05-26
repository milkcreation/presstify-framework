<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager;

use tiFy\Template\Factory\FactoryParams;

class Params extends FactoryParams
{
    /**
     * Instance du gabarit associé.
     * @var FileManager
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parse(): self
    {
        parent::parse();

        $this->set('attrs.data-control', 'file-manager');

        return $this;
    }
}