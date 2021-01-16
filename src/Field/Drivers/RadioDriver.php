<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;
use tiFy\Field\FieldDriverInterface;

class RadioDriver extends FieldDriver implements RadioDriverInterface
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
                 * @var bool|string $checked Activation de la selection.
                 */
                'checked' => 'on',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function isChecked(): bool
    {
        $checked = $this->get('checked', false);

        if (is_bool($checked)) {
            return $checked;
        } elseif ($this->has('value')) {
            return in_array($checked, (array)$this->getValue());
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function parseAttrValue(): FieldDriverInterface
    {
        if (($value = $this->get('checked')) && !is_bool($value)) {
            $this->set('attrs.value', $value);

            return $this;
        } else {
            return parent::parseAttrValue();
        }
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set('attrs.type', 'radio');

        if ($this->isChecked()) {
            $this->push('attrs', 'checked');
        }
        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->fieldManager()->resources('/views/radio');
    }
}