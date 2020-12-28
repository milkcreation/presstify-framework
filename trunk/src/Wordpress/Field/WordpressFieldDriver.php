<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Field;

use tiFy\Field\FieldDriver;

abstract class WordpressFieldDriver extends FieldDriver implements WordpressFieldDriverInterface
{
    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return __DIR__ . '/Resources/views/' . $this->getAlias();
    }
}