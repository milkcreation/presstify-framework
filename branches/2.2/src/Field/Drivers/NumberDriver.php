<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;

class NumberDriver extends FieldDriver implements NumberDriverInterface
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
                 * @var int $value
                 */
                'value' => 0,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set('attrs.type', 'number');

        return parent::render();
    }


    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->fieldManager()->resources('/views/number');
    }
}