<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;
use tiFy\Field\FieldDriverInterface;

class FileDriver extends FieldDriver implements FileDriverInterface
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                /**
                 * @var bool $multiple
                 */
                'multiple' => false,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function parseAttrName(): FieldDriverInterface
    {
        if ($name = $this->get('name')) {
            if ($this->get('multiple', false)) {
                $name = "{$name}[]";
            }
            $this->set('attrs.name', $name);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set('attrs.type', 'file');
        if ($this->get('multiple')) {
            $this->push('attrs', 'multiple');
        }
        return parent::render();
    }
}