<?php

namespace tiFy\Field\FieldOptions;

use Illuminate\Support\Arr;
use tiFy\Field\FieldItemInterface;
use tiFy\Kernel\Collection\AbstractCollection;

class FieldOptionsCollectionController extends AbstractCollection
{
    /**
     * Liste des éléments.
     * @var FieldOptionsItemController[]
     */
    protected $items = [];

    protected static $keys = 0;
    protected static $values = 0;

    /**
     * Affichage de la liste des éléments.
     *
     * @return void
     */
    public function display()
    {
        $items = [];
        foreach ($this->items as $item) :
            $items[] = $item->all();
        endforeach;

        return FieldOptionsCollectionWalker::display($items);
    }

    /**
     * Initialisation de la liste des éléments
     *
     * @return void
     */
    public function init()
    {
        $items = $this->items;
        $this->items = [];
        $force = !Arr::isAssoc($items);

        foreach ($items as $key => $item) :
            $this->parseItem($key, $item, '', $force);
        endforeach;
    }

    /**
     * Traitement d'un élément.
     *
     * @param $key
     * @param $item
     * @param string $parent
     *
     * @param bool $force
     */
    public function parseItem($key, $item, $parent = '', $force = false)
    {
        if ($item instanceof FieldOptionsItemController) :
            $this->items[] = $item;
        else :
            $index = self::$keys++;

            if (!is_array($item)) :
                $this->items[] = new FieldOptionsItemController(
                    $index,
                    [
                        'group'   => false,
                        'content' => $item,
                        'parent'  => $parent,
                        'value'   => $force ? $item : $key
                    ]
                );
            else :
                $this->items[] = new FieldOptionsItemController(
                    $index,
                    [
                        'group'   => true,
                        'content' => $key,
                        'parent'  => $parent
                    ]
                );

                $_force = !Arr::isAssoc($item);
                foreach ($item as $k => $i) :
                    $this->parseItem($k, $i, $index, $_force);
                endforeach;
            endif;
        endif;
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