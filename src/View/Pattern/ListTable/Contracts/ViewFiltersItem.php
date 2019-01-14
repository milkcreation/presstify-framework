<?php

namespace tiFy\View\Pattern\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface ViewFiltersItem extends ParamsBag
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Récupération du rendu de l'affichage.
     *
     * @return string
     */
    public function render();
}