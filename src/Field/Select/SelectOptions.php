<?php

namespace tiFy\Field\Select;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SelectOptions
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
     * @param null|boolean $associative
     */
    public function __construct($items = [])
    {
        foreach($items as $name => $attrs) :
            $this->_parseItem($name, $attrs);
        endforeach;
    }

    /**
     * Résolution de sortie de la classe sous la forme d'une chaîne de caractères.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     *
     */
    private function _parseItem($name, $attrs, $parent = '')
    {
        if ($attrs instanceof SelectOption) :
            $this->items[$name] = $attrs;
        elseif (!is_array($attrs)) :
            $this->items[$name] =  new SelectOption($name, ['content' => $attrs, 'parent' => $parent]);
        else :
            $this->items[$name] = new SelectOption($name, ['content' => $name, 'group' => true, 'parent' => $parent]);
            foreach($attrs as $_name => $_attrs) :
                $this->_parseItem($_name, $_attrs, $name);
            endforeach;
        endif;
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