<?php

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface Item extends ParamsBag
{
    /**
     * Récupération de la valeur de l'attribut de qualification de l'élément.
     *
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getPrimary($default = null);

    /**
     * Récupération de la clé d'indice de l'attribut de qualification de l'élément.
     *
     * @return mixed
     */
    public function getPrimaryKey();
}