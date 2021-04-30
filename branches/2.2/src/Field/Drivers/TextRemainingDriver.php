<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;
use tiFy\Support\Str;

class TextRemainingDriver extends FieldDriver implements TextRemainingDriverInterface
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
                 * @var bool $limit Activation de la limite de saisie selon le nombre maximum de caractères.
                 */
                'limit' => false,
                /**
                 * @var int $max Nombre maximum de caractères attendus. 150 par défaut.
                 */
                'max'   => 150,
                /**
                 * @var string $type string $selector Type de selecteur. textarea (défaut)|text.
                 */
                'type'  => 'textarea',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $type = $this->get('type', /** compat */ $this->get('selector'));

        $this->set(
            'attrs.class',
            trim(
                sprintf(
                    $this->get('attrs.class', '%s'),
                    ' FieldTextRemaining FieldTextRemaining--' . $type
                )
            )
        );

        $this->set('attrs.data-id', $this->getId());

        $this->set('attrs.data-control', 'text-remaining');

        $this->set('tag', $type === 'textarea' ? 'textarea' : 'input');

        if ($type !== 'textarea') {
            $this->set('attrs.type', $type);
        }

        $this->set(
            'attrs.data-options',
            [
                'infos' => [
                    'plural'   => __('restants', 'tify'),
                    'singular' => __('restant', 'tify'),
                    'none'     => __('restant', 'tify'),
                ],
                'limit' => $this->get('limit'),
                'max'   => $this->get('max'),
            ]
        );

        switch ($this->get('tag')) {
            case 'textarea' :
                $this->set('content', Str::br2nl($this->get('value') ?: ''));
                break;
            default:
                $this->set('attrs.value', $this->get('value') ?: '');
                break;
        }
        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->fieldManager()->resources('/views/text-remaining');
    }
}