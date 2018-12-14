<?php

namespace tiFy\View\Pattern\ListTable\RowActions;

use tiFy\Kernel\Collection\Collection;
use tiFy\View\Pattern\ListTable\Contracts\Item;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;
use tiFy\View\Pattern\ListTable\Contracts\RowActionsItem;
use tiFy\View\Pattern\ListTable\Contracts\RowActionsCollection as RowActionsCollectionContract;

class RowActionsCollection extends Collection implements RowActionsCollectionContract
{
    /**
     * Instance de l'élément associé.
     * @var Item
     */
    protected $item = [];

    /**
     * Liste des actions par ligne.
     * @var void|RowActionsItem[]
     */
    protected $items = [];

    /**
     * Instance du motif d'affichage associé.
     * @var ListTable
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $row_actions Liste des éléments
     * @param Item $item Instance de l'élément associé.
     * @param ListTable $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($row_actions, Item $item, ListTable $pattern)
    {
        $this->item = $item;
        $this->pattern = $pattern;

        $this->parse($row_actions);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->display();
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        $actions = $this->collect()->filter(function (RowActionsItem $item) {
            return $item->isActive();
        });

        if (!$action_count = count($actions)) :
            return '';
        endif;

        $i = 0;
        $always_visible = $this->pattern->param('row_actions_always_visible');

        $output = '';
        $output .= "<div class=\"" . ($always_visible ? 'row-actions visible' : 'row-actions') . "\">";
        foreach ($actions as $action => $link) :
            ++$i;
            ($i == $action_count) ? $sep = '' : $sep = ' | ';
            $output .= "<span class=\"{$action}\">{$link}{$sep}</span>";
        endforeach;

        $output .= "</div>";

        $output .= "<button type=\"button\" class=\"toggle-row\"><span class=\"screen-reader-text\">" .
            __('Show more details') .
            "</span></button>";

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($row_actions = [])
    {
        if ($row_actions) :
            foreach ($row_actions as $name => $attrs) :
                if (is_numeric($name)) :
                    $name = $attrs;
                    $attrs = [];
                elseif (is_string($attrs)) :
                    $attrs = ['content' => $attrs];
                endif;

                $alias = $this->pattern->has("row-actions.item.{$name}")
                    ? "row-actions.item.{$name}"
                    : 'row-actions.item';

                $this->items[$name] = $this->pattern->get($alias, [$name, $attrs, $this->item, $this->pattern]);
            endforeach;

            $this->items = array_filter($this->items, function ($value) {
                return (string)$value !== '';
            });
        endif;
    }
}