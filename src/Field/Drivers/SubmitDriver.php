<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;

class SubmitDriver extends FieldDriver implements SubmitDriverInterface
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
                'value' => __('Envoyer', 'tify'),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set('attrs.type', 'submit');

        return parent::render();
    }
}