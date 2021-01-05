<?php

declare(strict_types=1);

namespace tiFy\Metabox\Contexts;

use tiFy\Metabox\MetaboxContext;
use tiFy\Metabox\MetaboxDriverInterface;

class TabContext extends MetaboxContext implements TabContextInterface
{
    /**
     * Onglet actif.
     * @var string
     */
    protected $active = '';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'rotation' => ['left', 'top', 'default', 'pills'],
            ]
        );
    }

    /**
     * @inheritDoc
    */
    public function render(): string
    {
        if ($drivers = $this->getDrivers()) {
            $items = [];
            array_walk(
                $drivers,
                function (MetaboxDriverInterface $driver) use (&$items) {
                    $items[$driver->getAlias()] = [
                        'name'     => $driver->getAlias(),
                        'title'    => $driver->getTitle(),
                        'parent'   => $driver->getParent(),
                        'content'  => "<div class=\"MetaboxTab-content\">{$driver->render()}</div>",
                        'position' => $driver->getPosition(),
                    ];
                }
            );
            $this->params(compact('items'));
        }
        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function setActive(string $tab): TabContextInterface
    {
        $this->active = $tab;

        return $this;
    }
}
