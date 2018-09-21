<?php

namespace tiFy\Components\Layout\ListTable\Item;

use tiFy\Kernel\Item\ItemInterface as KernelItemInterface;
use tiFy\Kernel\Item\ItemIteratorInterface;

interface ItemInterface extends ItemIteratorInterface
{
    /**
     * Récupération de la valeur de l'attribut de qualification de l'élément.
     *
     * @return mixed
     */
    public function getPrimary();
}