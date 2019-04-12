<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\RowActions;

use tiFy\Support\Collection;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;
use tiFy\Template\Templates\ListTable\Contracts\RowActionsCollection as RowActionsCollectionContract;
use tiFy\Template\Templates\ListTable\Contracts\RowActionsItem;

class RowActionsCollection extends Collection implements RowActionsCollectionContract
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * Liste des actions par ligne.
     * @var array|RowActionsItem[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param ListTable $factory Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct(ListTable $factory)
    {
        $this->factory = $factory;

        $attrs = $this->factory->param('row_actions', []);

        $this->parse(is_array($attrs) ? $attrs : []);
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return (string)$this->render();
    }

    /**
     * @inheritdoc
     */
    public function parse(array $row_actions = []): RowActionsCollectionContract
    {
        if ($row_actions) {
            foreach ($row_actions as $name => $attrs) {
                if (is_numeric($name)) {
                    $name = $attrs;
                    $attrs = [];
                } elseif (is_string($attrs)) {
                    $attrs = ['content' => $attrs];
                }

                $alias = $this->factory->bound("row-actions.item.{$name}")
                    ? "row-actions.item.{$name}"
                    : 'row-actions.item';

                $this->items[$name] = $this->factory->resolve($alias, [$name, $attrs, $this->factory]);
            }

            $this->items = array_filter($this->items, function ($value) {
                return (string)$value !== '';
            });
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        $actions = $this->collect()->filter(function (RowActionsItem $item) {
            return $item->isActive();
        });

        if ($action_count = count($actions)) {
            $i = 0;
            $always_visible = $this->factory->param('row_actions_always_visible');

            $output = '';
            $output .= "<div class=\"" . ($always_visible ? 'row-actions visible' : 'row-actions') . "\">";
            foreach ($actions as $action => $link) {
                ++$i;
                ($i == $action_count) ? $sep = '' : $sep = ' | ';
                $output .= "<span class=\"{$action}\">{$link}{$sep}</span>";
            }

            $output .= "</div>";

            $output .= "<button type=\"button\" class=\"toggle-row\"><span class=\"screen-reader-text\">" .
                __('Voir plus de détails', 'tify') .
                "</span></button>";

            return $output;
        } else {
            return '';
        }
    }
}