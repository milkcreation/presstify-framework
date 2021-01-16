<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;

class ColorpickerDriver extends FieldDriver implements ColorpickerDriverInterface
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
                 * @see https://bgrins.github.io/spectrum/
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
                'attrs.data-control' => 'colorpicker',
                'attrs.data-options' => array_merge(
                    [
                        'preferredFormat' => 'hex',
                        'showInput'       => true,
                    ],
                    $this->get('options', [])
                ),
            ]
        );
        return parent::render();
    }


    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->fieldManager()->resources('/views/colorpicker');
    }
}