<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\Drivers\CheckboxCollection\CheckboxChoiceInterface;
use tiFy\Field\Drivers\CheckboxCollection\CheckboxWalker;
use tiFy\Field\Drivers\CheckboxCollection\CheckboxWalkerInterface;
use tiFy\Field\FieldDriver;

class CheckboxCollectionDriver extends FieldDriver implements CheckboxCollectionDriverInterface
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
                 * @var array|CheckboxDriverInterface[]|CheckboxChoiceInterface[]|CheckboxWalkerInterface $choices Liste de choix.
                 */
                'choices' => [],
            ]
        );
    }

    /**
     * @inheritDoc
     *
     * @todo utiliser self::parseAttrName
     */
    public function getName(): string
    {
        $name = $this->get('attrs.name') ?: $this->get('name');

        return "{$name}[]";
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        parent::parseParams();

        $choices = $this->get('choices', []);
        if (!$choices instanceof CheckboxWalkerInterface) {
            $choices = new CheckboxWalker($choices);
        }
        $this->set('choices', $choices->setField($this)->build());

        return parent::render();
    }
}