<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;

class HiddenDriver extends FieldDriver implements HiddenDriverInterface
{
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set('attrs.type', 'hidden');

        return parent::render();
    }
}