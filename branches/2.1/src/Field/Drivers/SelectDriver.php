<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\Drivers\Select\SelectChoiceInterface;
use tiFy\Field\Drivers\Select\SelectChoices;
use tiFy\Field\Drivers\Select\SelectChoicesInterface;
use tiFy\Field\FieldDriver;
use tiFy\Field\FieldDriverInterface;

class SelectDriver extends FieldDriver implements SelectDriverInterface
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
                 * @var string[]|array|SelectChoiceInterface[]|SelectChoicesInterface $choices Liste de selection d'éléments.
                 */
                'choices'  => [],
                /**
                 * @var bool $multiple Activation de la liste de selection multiple.
                 */
                'multiple' => false,
                /**
                 *
                 */
                'wrapper'  => false,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        $value = $this->get('value', null);

        if (is_null($value)) {
            return null;
        }

        if (!is_array($value)) {
            $value = array_map('trim', explode(',', (string)$value));
        }

        $value = array_unique($value);

        if (!$this->get('multiple')) {
            $value = [reset($value)];
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function parseAttrName(): FieldDriverInterface
    {
        if ($name = $this->get('name')) {
            $this->set('attrs.name', $this->get('multiple') ? "{$name}[]" : $name);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $choices = $this->get('choices', []);
        if (!$choices instanceof SelectChoicesInterface) {
            $this->set('choices', new SelectChoices($choices, $this->getValue()));
        }

        if ($this->get('multiple')) {
            $this->push('attrs', 'multiple');
        }
        return parent::render();
    }
}