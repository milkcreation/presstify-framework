<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;

class PasswordDriver extends FieldDriver implements PasswordDriverInterface
{
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set('attrs.type', 'password');

        return parent::render();
    }
}