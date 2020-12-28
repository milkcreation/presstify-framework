<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\Drivers\Dropdown\DropdownCollection;
use tiFy\Partial\Drivers\Dropdown\DropdownCollectionInterface;
use tiFy\Partial\PartialDriver;
use tiFy\Partial\PartialDriverInterface;

class DropdownDriver extends PartialDriver implements DropdownDriverInterface
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            'button'    => '',
            'items'     => [],
            'open'      => false,
            'trigger'   => false
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        parent::parseParams();

        $this->set('attrs.data-control', 'dropdown');
        $this->set('attrs.data-id', $this->getId());

        $classes = [
            'button'    => 'Dropdown-button',
            'listItems' => 'Dropdown-items',
            'item'      => 'Dropdown-item'
        ];
        foreach($classes as $key => &$class) {
            $class = sprintf($this->get("classes.{$key}", '%s'), $class);
        }
        $this->set('classes', $classes);

        $items = $this->get('items', []);

        if (!$items instanceof DropdownCollectionInterface) {
            $items = new DropdownCollection($items);
        }
        $this->set('items', $items->setPartial($this));

        $this->set('attrs.data-options', [
            'classes' => $this->get('classes', []),
            'open'    => $this->get('open'),
            'trigger' => $this->get('trigger'),
        ]);

        return $this;
    }
}