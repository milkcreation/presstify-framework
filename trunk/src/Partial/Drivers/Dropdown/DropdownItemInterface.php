<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\Dropdown;

use tiFy\Contracts\Support\ParamsBag;

interface DropdownItemInterface extends ParamsBag
{
    /**
     * Résolution de sortie du controleur sous la forme d'une chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Récupération du contenu d'affichage de l'élément
     *
     * @return string
     */
    public function getContent(): string;
}
