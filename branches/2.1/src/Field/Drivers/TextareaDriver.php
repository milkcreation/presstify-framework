<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;
use tiFy\Field\FieldDriverInterface;

class TextareaDriver extends FieldDriver implements TextareaDriverInterface
{
    /**
     * @inheritDoc
     */
    public function parse(): FieldDriverInterface
    {
        return $this->parseAttrId()->parseAttrClass()->parseAttrName();
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set('content', $this->get('value'));

        return parent::render();
    }
}