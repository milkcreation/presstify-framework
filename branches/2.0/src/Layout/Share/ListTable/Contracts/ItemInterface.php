<?php

namespace tiFy\Layout\Share\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParametersBagIteratorInterface;

interface ItemInterface extends ParametersBagIteratorInterface
{
    /**
     * Récupération de la valeur de l'attribut de qualification de l'élément.
     *
     * @return mixed
     */
    public function getPrimary();
}