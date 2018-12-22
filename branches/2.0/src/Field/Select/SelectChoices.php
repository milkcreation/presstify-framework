<?php

namespace tiFy\Field\Select;

use Illuminate\Support\Arr;
use tiFy\Contracts\Field\SelectChoices as SelectChoicesContract;
use tiFy\Kernel\Collection\Collection;

class SelectChoices extends Collection implements SelectChoicesContract
{
    /**
     * Liste des éléments.
     * @var SelectChoice[]
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
        if (is_string($attrs)) :
            $this->wrap($name, ['content' => $attrs, 'parent' => $parent]);
        elseif (is_array($attrs)) :
            $this->wrap($name, ['content' => $name, 'group' => true, 'parent' => $parent]);
            foreach($attrs as $_name => $_attrs) :
                $this->recursiveWrap($_name, $_attrs, $name);
            endforeach;
        else :
            $this->wrap($name, $attrs);
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

            $this->collect()->each(function (SelectChoice $item) use ($selected) {
                if (!$item->isGroup() && in_array($item->getValue(), $selected)) :
                    $item->push('attrs', 'selected');
                endif;
            });
        endif;

        if (!$this->collect()->first(function(SelectChoice $item) { return $item->isSelected(); })) :
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

    /**
     * {@inheritdoc}
     */
    public function wrap($name, $item)
    {
        if (!$item instanceof SelectChoice) :
            $item = new SelectChoice($name, $item);
        endif;

        return $this->items[] = $item;
    }
}