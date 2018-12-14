<?php

namespace tiFy\View\Pattern\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface Item extends ParamsBag
{
    /**
     * Récupération de la valeur de l'attribut de qualification de l'élément.
     *
     * @return mixed
     */
    public function getPrimary();
}