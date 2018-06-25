<?php

namespace tiFy\Components\AdminView\ListTable\RowAction;

use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemController;
use tiFy\AdminView\AdminViewControllerInterface;
use tiFy\Components\AdminView\ListTable\Item\ItemInterface;
use tiFy\Components\AdminView\ListTable\ListTableInterface;

class RowActionCollectionController
{
    /**
     * Classe de rappel de la vue associée.
     * @var ListTableInterface
     */
    protected $app;

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
     * @param AdminViewControllerInterface $app Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($item, ListTableInterface $app)
    {
        $this->app = $app;
        $this->item = $item;

        $this->parse($this->app->param('row_actions', []));
    }

    /**
     * Récupération de la liste des actions par ligne.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Traitement de la liste des actions par ligne.
     *
     * @param array $row_actions Liste des actions par ligne.
     *
     * @return void
     */
    public function parse($row_actions = [])
    {
        foreach ($row_actions as $name => $attrs) :
            if (is_numeric($name)) :
                $name = $attrs;
                $attrs = [];
            elseif (is_string($attrs)) :
                $attrs['content'] = $attrs;
            else :
                $this->items[$name] = new $controller($name, $attrs, $this->item, $this->app);
            endif;

            $provide = $this->app->provider()->has("row_actions.item.{$name}")
                ? "row_actions.item.{$name}"
                : 'row_actions.item';

            $this->items[$name] = $this->app->provide($provide, [$name, $attrs, $this->item, $this->app]);
        endforeach;

        $this->items = array_filter($this->items, function ($value) {
            return (string)$value !== '';
        });
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        $actions = $this->all();

        if (!$action_count = count($actions)) :
            return '';
        endif;

        $i = 0;
        $always_visible = $this->app->param('row_actions_always_visible');

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
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->display();
    }
}