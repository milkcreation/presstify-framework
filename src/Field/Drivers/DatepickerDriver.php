<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;

class DatepickerDriver extends FieldDriver implements DatepickerDriverInterface
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
                 * @var array $options Liste des options du contrôleur ajax.
                 */
                'options' => [],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set(
            [
                'attrs.data-control' => 'datepicker',
                'attrs.data-options' => $this->get('options', []),
            ]
        );
        return parent::render();
    }
}