<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;

class ButtonDriver extends FieldDriver implements ButtonDriverInterface
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
                 * @var string $content Contenu de la balise HTML.
                 */
                'content' => __('Envoyer', 'tify'),
                /**
                 * @var string $type Type de bouton. button par défaut.
                 */
                'type'    => 'button',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if (!$this->has('attrs.type')) {
            $this->set('attrs.type', $this->get('type', 'button'));
        }
        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->fieldManager()->resources('/views/button');
    }
}