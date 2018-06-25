<?php

namespace tiFy\Components\AdminView\ListTable\Item;

interface ItemInterface
{
    /**
     * Récupération de la valeur de l'attribut de qualification de l'élément.
     *
     * @return mixed
     */
    public function getPrimary();
}