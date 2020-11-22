<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager;

use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\FileManager\Contracts\{Factory, Sidebar as SidebarContract};

class Sidebar implements SidebarContract
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
    public function __toString(): string
    {
        return (string)$this->factory->viewer('sidebar', ['file' => $this->factory->getFile()]);
    }
}