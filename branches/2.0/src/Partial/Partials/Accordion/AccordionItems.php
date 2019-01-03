<?php

namespace tiFy\Partial\Partials\Accordion;

use tiFy\Contracts\Partial\Accordion;
use tiFy\Contracts\Partial\AccordionItems as AccordionItemsContract;
use tiFy\Kernel\Collection\QueryCollection;

class AccordionItems extends QueryCollection implements AccordionItemsContract
{
    /**
     * Instance du controleur d'affichage.
     * @var Accordion
     */
    protected $partial;

    /**
     * CONSTRUCTEUR.
     *
     * @param mixed $items Liste des éléments.
     * @param null $selected Liste des éléments sélectionné.
     *
     * @return void
     */
    public function __construct($items, $selected = null)
    {
        $this->query($items);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function query($args)
    {
        array_walk($args, [$this, 'wrap']);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->walk($this->items, 0, null);
    }

    /**
     * {@inheritdoc}
     */
    public function setPartial(Accordion $partial)
    {
        $this->partial = $partial;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function walk($items = [], $depth = 0, $parent = null)
    {
        $opened = false;

        $output = "";
        foreach ($items as $item) :
            if ($item->getParent() !== $parent) :
                continue;
            endif;

            if (!$opened) :
                $output = "<ul class=\"PartialAccordion-items PartialAccordion-items--{$depth}\" data-control=\"accordion.items\">";
                $opened = true;
            endif;

            $item->setDepth($depth);

            $output .= "<li class=\"PartialAccordion-item PartialAccordion-item--{$item->getName()}\" data-control=\"accordion.item\">";
            $output .= (string) $this->partial->viewer('item', compact('item'));
            $output .= $this->walk($items, ($depth + 1), $item->getName());
            $output .= "</li>";
        endforeach;

        if ($opened) :
            $output .= "</ul>";
        endif;

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function wrap($item, $key = null)
    {
        if (!$item instanceof AccordionItem) :
            $item = new AccordionItem($key, $item);
        endif;

        return $this->items[$key] = $item;
    }
}