<?php

namespace tiFy\Layout\Share\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParametersBagInterface;

interface ViewFilterItemInterface extends ParametersBagInterface
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Affichage.
     *
     * @return string
     */
    public function display();
}