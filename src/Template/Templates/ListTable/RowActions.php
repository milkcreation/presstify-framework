<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Support\Collection;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;
use tiFy\Template\Templates\ListTable\Contracts\{RowAction, RowActions as RowActionsContract};

class RowActions extends Collection implements RowActionsContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * Liste des actions par ligne.
     * @var array|RowAction[]
     */
    protected $items = [];

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return (string)$this->render();
    }

    /**
     * @inheritDoc
     */
    public function parse(array $row_actions = []): RowActionsContract
    {
        if ($row_actions) {
            foreach ($row_actions as $name => $attrs) {
                if (is_numeric($name)) {
                    $name = $attrs;
                    $attrs = [];
                } elseif (is_string($attrs)) {
                    $attrs = ['content' => $attrs];
                }

                $alias = $this->factory->bound("row-action.{$name}")
                    ? "row-action.{$name}"
                    : 'row-action';

                $this->items[$name] = $this->factory->resolve($alias, [$name, $attrs]);
            }

            $this->items = array_filter($this->items, function ($value) {
                return (string)$value !== '';
            });
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $actions = $this->collect()->filter(function (RowAction $item) {
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