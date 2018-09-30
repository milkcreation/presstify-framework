<?php

namespace tiFy\Layout\Share\ListTable\RowAction;

use tiFy\Layout\Share\ListTable\Contracts\ItemInterface;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;
use tiFy\Layout\Share\ListTable\Contracts\RowActionCollectionInterface;

class RowActionCollectionController implements RowActionCollectionInterface
{
    /**
     * Instance de la disposition associée.
     * @var ListTableInterface
     */
    protected $layout;

    /**
     * Données de l'élément courant.
     * @var ItemInterface
     */
    protected $item = [];

    /**
     * Liste des actions par ligne.
     * @var void|RowActionItemController[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param ItemInterface $item Données de l'élément courant.
     * @param ListTableInterface $layout Instance de la disposition associée.
     *
     * @return void
     */
    public function __construct($item, ListTableInterface $layout)
    {
        $this->item = $item;
        $this->layout = $layout;

        $this->parse($this->layout->param('row_actions', []));
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->display();
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
        $actions = $this->all();

        if (!$action_count = count($actions)) :
            return '';
        endif;

        $i = 0;
        $always_visible = $this->layout->param('row_actions_always_visible');

        $output = '';
        $output .= "<div class=\"" . ($always_visible ? 'row-actions visible' : 'row-actions') . "\">";
        foreach ($actions as $action => $link) :
            ++$i;
            ($i == $action_count) ? $sep = '' : $sep = ' | ';
            $output .= "<span class=\"{$action}\">{$link}{$sep}</span>";
        endforeach;

        $output .= "</div>";

        $output .= "<button type=\"button\" class=\"toggle-row\"><span class=\"screen-reader-text\">" . __('Show more details') . "</span></button>";

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
                    $attrs['content'] = $attrs;
                else :
                    /** @todo */
                    //$this->items[$name] = new $controller($name, $attrs, $this->item, $this->layout);
                endif;

                $alias = $this->layout->bound("row_actions.item.{$name}")
                    ? "row_actions.item.{$name}"
                    : 'row_actions.item';

                $this->items[$name] = $this->layout->resolve($alias, [$name, $attrs, $this->item, $this->layout]);
            endforeach;

            $this->items = array_filter($this->items, function ($value) {
                return (string)$value !== '';
            });
        endif;
    }
}