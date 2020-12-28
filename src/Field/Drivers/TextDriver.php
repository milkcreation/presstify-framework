<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;

class TextDriver extends FieldDriver implements TextDriverInterface
{
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if (!$this->get('attrs.type')) {
            $this->set('attrs.type', 'text');
        }
        return parent::render();
    }
}