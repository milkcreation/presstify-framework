<?php declare(strict_types=1);

namespace tiFy\Wordpress\Partial;

use tiFy\Partial\PartialDriver;

abstract class WordpressPartialDriver extends PartialDriver implements WordpressPartialDriverInterface
{
    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return __DIR__ . '/Resources/views/' . $this->getAlias();
    }
}