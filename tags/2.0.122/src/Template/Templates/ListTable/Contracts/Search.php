<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface Search extends ParamsBag
{
    /**
     * Résolution de sortie d'une instance de la classe sous la forme d'une chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Affichage.
     *
     * @return string
     */
    public function render(): string;
}