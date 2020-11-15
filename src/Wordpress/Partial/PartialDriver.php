<?php declare(strict_types=1);

namespace tiFy\Wordpress\Partial;

use tiFy\Partial\PartialDriver as BasePartialDriver;

abstract class PartialDriver extends BasePartialDriver
{
    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return __DIR__ . '/Resources/views/' . $this->getAlias();
    }
}