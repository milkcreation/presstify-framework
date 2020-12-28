<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\Accordion;

use Illuminate\Support\Collection;
use tiFy\Partial\Drivers\AccordionDriverInterface;
use tiFy\Support\Arr;
use tiFy\Support\HtmlAttrs;

class AccordionCollection implements AccordionCollectionInterface
{
    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    private $built = false;

    /**
     * Liste des éléments.
     * @var AccordionItemInterface[]|array
     */
    protected $items = [];

    /**
     * Identifiant de qualification du parent initial.
     * @var string|null
     */
    protected $parent;

    /**
     * Instance du controleur d'affichage.
     * @var AccordionDriverInterface
     */
    protected $partial;

    /**
     * @param mixed $items Liste des éléments.
     */
    public function __construct($items)
    {
        array_walk($items, function ($item, $key) {
            $this->setItem($item, $key);
        });
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function build(): AccordionCollectionInterface
    {
        if (!$this->built) {
            if ($this->exists()) {
                foreach ($this->items as $item) {
                    $item->setWalker($this)->build();
                }

                $this->registerOpened();
            }

            $this->built = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function exists(): bool
    {
        return !!$this->items;
    }

    /**
     * @inheritDoc
     */
    public function registerOpened(): AccordionCollectionInterface
    {
        $opened = $this->partial->get('opened');
        $collect = new Collection($this->items);

        if (!is_null($opened)) {
            $opened = Arr::wrap($opened);

            $collect->each(function (AccordionItem $item) use ($opened) {
                if (in_array($item->getId(), $opened)) {
                    $item->setOpened();
                }
            });
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->walk($this->items, 0, $this->parent);
    }

    /**
     * @inheritDoc
     */
    public function setItem($item, $key = null): AccordionItemInterface
    {
        if (!$item instanceof AccordionItemInterface) {
            $item = new AccordionItem($key, $item);
        }

        return $this->items[$key] = $item;
    }

    /**
     * @inheritDoc
     */
    public function setPartial(AccordionDriverInterface $partial): AccordionCollectionInterface
    {
        $this->partial = $partial;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParent(?string $parent = null): AccordionCollectionInterface
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function walk($items = [], $depth = 0, ?string $parent = null): string
    {
        $opened = false;
        $output = '';

        foreach ($items as $item) {
            if ($item->getParent() !== $parent) {
                continue;
            }

            if (!$opened) {
                $output = "<ul class=\"Accordion-items Accordion-items--{$depth}\" data-control=\"accordion.items\">";
                $opened = true;
            }

            $item->setDepth($depth);

            $attrs = [
                'class'        => "Accordion-item Accordion-item--{$item->getId()}",
                'data-control' => 'accordion.item',
                'aria-open'    => $item->isOpened() ? 'true' : 'false'
            ];

            $output .= "<li " . HtmlAttrs::createFromAttrs($attrs) . ">";
            $output .= $this->partial->view('item', compact('item'));
            $output .= $this->walk($items, ($depth + 1), (string)$item->getId());
            $output .= "</li>";
        }

        if ($opened) {
            $output .= "</ul>";
        }

        return $output;
    }
}