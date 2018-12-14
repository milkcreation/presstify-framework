<?php

namespace tiFy\Contracts\View;

use tiFy\Contracts\Kernel\ParamsBag;

interface PatternFactory extends ParamsBag
{
    /**
     * Récupération de contenu d'affichage de la vue.
     *
     * @return string|PatternController
     */
    public function getContent();

    /**
     * Récupération du nom de qualification de la disposition associée.
     *
     * @return string
     */
    public function getName();
}