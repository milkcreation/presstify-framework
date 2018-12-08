<?php

namespace tiFy\Field;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SelectOptions
{
    /**
     * Liste des éléments.
     * @var FieldOptionsItemController[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $items
     * @param null|boolean $associative
     */
    public function __construct($items = [])
    {
        foreach($items as $name => &$attrs) :
            if (!$attrs instanceof SelectOption) :
                $attrs = new SelectOption($name, $attrs);
            endif;
        endforeach;

        $this->items = $items;
    }

    /**
     *
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Itérateur d'affichage.
     *
     * @param SelectOption[] $items Liste des éléments à ordonner.
     * @param int $depth Niveau de profondeur.
     * @param string $parent Nom de qualification de l'élément parent.
     *
     * @return string
     */
    private function _walk($items = [], $depth = 0, $parent = '')
    {
        $output = "";
        foreach ($items as $item) :
            if ($item->getParent() !== $parent) :
                continue;
            endif;

            $item->setDepth($depth);

            $output .= $item->tagOpen();
            $output .= $item->tagContent();
            $output .= $this->_walk($items, ($depth + 1), $item->getName());
            $output .= $item->tagClose();
        endforeach;

        return $output;
    }

    /**
     * @return Collection
     */
    public function collect()
    {
        return new Collection($this->items);
    }

    /**
     * Définition de liste des éléments selectionnés.
     *
     * @param mixed $selected
     *
     * @return $this
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
     * Affichage de la liste des éléments.
     *
     * @return string
     */
    public function render()
    {
        return $this->_walk($this->items);
    }
}