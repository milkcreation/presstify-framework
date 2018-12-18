<?php

namespace tiFy\View\Pattern\ListTable\RowActions;

use tiFy\Kernel\Collection\Collection;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;
use tiFy\View\Pattern\ListTable\Contracts\RowActionsItem;
use tiFy\View\Pattern\ListTable\Contracts\RowActionsCollection as RowActionsCollectionContract;

class RowActionsCollection extends Collection implements RowActionsCollectionContract
{
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
     * @param ListTable $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($row_actions, ListTable $pattern)
    {
        $this->pattern = $pattern;

        $this->parse($row_actions);
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

                $alias = $this->pattern->bound("row-actions.item.{$name}")
                    ? "row-actions.item.{$name}"
                    : 'row-actions.item';

                $this->items[$name] = $this->pattern->resolve($alias, [$name, $attrs, $this->pattern]);
            endforeach;

            $this->items = array_filter($this->items, function ($value) {
                return (string)$value !== '';
            });
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $actions = $this->collect()->filter(function (RowActionsItem $item) {
            return $item->isActive();
        });

        if ($action_count = count($actions)) :
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
                __('Voir plus de détails', 'tify') .
                "</span></button>";

            return $output;
        else :
            return '';
        endif;
    }
}