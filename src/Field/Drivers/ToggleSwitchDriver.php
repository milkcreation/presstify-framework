<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;

class ToggleSwitchDriver extends FieldDriver implements ToggleSwitchDriverInterface
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
                 * @var string $value
                 */
                'value'     => 'on',
                /**
                 * @var string $label_on
                 */
                'label_on'  => _x('Oui', 'FieldToggleSwitch', 'tify'),
                /**
                 * @var string $label_off
                 */
                'label_off' => _x('Non', 'FieldToggleSwitch', 'tify'),
                /**
                 * @var bool|int|string $value_on
                 */
                'value_on'  => 'on',
                /**
                 * @var bool|int|string $value_off
                 */
                'value_off' => 'off',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set('attrs.data-control', 'toggle-switch');

        return parent::render();
    }
}