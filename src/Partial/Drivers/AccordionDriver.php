<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\Drivers\Accordion\AccordionCollection;
use tiFy\Partial\Drivers\Accordion\AccordionCollectionInterface;
use tiFy\Partial\Drivers\Accordion\AccordionItemInterface;
use tiFy\Partial\PartialDriver;
use tiFy\Partial\PartialDriverInterface;

class AccordionDriver extends PartialDriver implements AccordionDriverInterface
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var array|AccordionItemInterface[]|AccordionCollectionInterface $items Liste des éléments.
             */
            'items'     => [],
            /**
             * @var bool $multiple Activation de l'ouverture multiple d'éléments.
             */
            'multiple'  => false,
            /**
             *
             */
            'parent'    => null,
            /**
             * @var mixed $opened Définition de la liste des éléments ouverts à l'initialisation.
             */
            'opened'    => null,
            /**
             * @var string $theme Theme d'affichage. light|dark.
             */
            'theme'     => 'light',
            /**
             * @var bool $triggered Activation de l'ouverture et la fermeture du volet par un déclencheur dédié.
             */
            'triggered' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        parent::parseParams();

        if ($theme = $this->get('theme')) {
            $this->set('attrs.class', trim($this->get('attrs.class') . " Accordion--{$theme}"));
        }

        $this->set([
            'attrs.data-control' => 'accordion',
            'attrs.data-id'      => $this->getId(),
            'attrs.data-options' => [
                'multiple'  => $this->get('multiple'),
                'opened'    => $this->get('opened'),
                'triggered' => $this->get('triggered'),
            ],
        ]);

        $items = $this->get('items', []);
        if (!$items instanceof AccordionCollectionInterface) {
            $items = new AccordionCollection($items);
        }

        $parent = !is_null($this->get('parent', null)) ? (string) $this->get('parent', null) : null;

        $this->set('items', $items->setPartial($this)->setParent($parent)->build());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->partialManager()->resources("/views/accordion");
    }
}