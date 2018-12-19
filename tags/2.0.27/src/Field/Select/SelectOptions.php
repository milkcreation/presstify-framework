<?php

namespace tiFy\Field\Select;

use Illuminate\Support\Arr;
use tiFy\Contracts\Field\SelectOptions as SelectOptionsContract;
use tiFy\Kernel\Collection\Collection;

class SelectOptions extends Collection implements SelectOptionsContract
{
    /**
     * Liste des éléments.
     * @var SelectOption[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $items
     * @param mixed $selected Liste des éléments selectionnés
     */
    public function __construct($items = [], $selected = null)
    {
        foreach($items as $name => $attrs) :
            $this->recursiveWrap($name, $attrs);
        endforeach;

        $this->setSelected($selected);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function recursiveWrap($name, $attrs, $parent = null)
    {
        if ($attrs instanceof SelectOption) :
            $this->items[$name] = $attrs;
        elseif (!is_array($attrs)) :
            $this->items[$name] = new SelectOption($name, ['content' => $attrs, 'parent' => $parent]);
        else :
            $this->items[$name] = new SelectOption($name, ['content' => $name, 'group' => true, 'parent' => $parent]);
            foreach($attrs as $_name => $_attrs) :
                $this->recursiveWrap($_name, $_attrs, $name);
            endforeach;
        endif;
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
    public function setSelected($selected = null)
    {
        if (!is_null($selected)) :
            $selected = Arr::wrap($selected);

            $this->collect()->each(function (SelectOption $item) use ($selected) {
                if (!$item->isGroup() && in_array($item->getValue(), $selected)) :
                    $item->push('attrs', 'selected');
                endif;
            });
        endif;

        if (!$this->collect()->first(function(SelectOption $item) { return $item->isSelected(); })) :
            if ($first = $this->collect()->first()) :
                $first->push('attrs', 'selected');
            endif;
        endif;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function walk($items = [], $depth = 0, $parent = null)
    {
        $output = "";
        foreach ($items as $item) :
            if ($item->getParent() !== $parent) :
                continue;
            endif;

            $item->setDepth($depth);

            $output .= $item->tagOpen();
            $output .= $item->tagContent();
            $output .= $this->walk($items, ($depth + 1), $item->getName());
            $output .= $item->tagClose();
        endforeach;

        return $output;
    }
}