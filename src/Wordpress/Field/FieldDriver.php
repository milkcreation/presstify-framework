<?php declare(strict_types=1);

namespace tiFy\Wordpress\Field;

use tiFy\Field\FieldDriver as BaseFieldDriver;

abstract class FieldDriver extends BaseFieldDriver
{
    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return __DIR__ . '/Resources/views/' . $this->getAlias();
    }
}