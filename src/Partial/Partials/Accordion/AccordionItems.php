<?php

namespace tiFy\Partial\Partials\Accordion;

use tiFy\Contracts\View\ViewEngine;
use tiFy\Kernel\Collection\QueryCollection;

class AccordionItems extends QueryCollection
{
    /**
     * Instance du controleur d'affichage des gabarits
     * @var ViewEngine
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $items Liste des éléments.
     * @param ViewEngine $viewer Instance du controleur d'affichage des gabarits.
     * @param null $selected Liste des éléments sélectionné.
     *
     * @return void
     */
    public function __construct($items, ViewEngine $viewer, $selected = null)
    {
        $this->viewer = $viewer;

        array_walk($items, [$this, 'wrap']);
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
            $output .= $this->viewer->make('accordion-item', $item->all());
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
    public function render()
    {
        return $this->walk($this->items);
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