<?php

namespace tiFy\Layout\Share\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface ItemInterface extends ParamsBag
{
    /**
     * Récupération de la valeur de l'attribut de qualification de l'élément.
     *
     * @return mixed
     */
    public function getPrimary();
}