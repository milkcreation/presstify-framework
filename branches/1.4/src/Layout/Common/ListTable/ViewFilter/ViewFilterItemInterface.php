<?php

namespace tiFy\Components\Layout\ListTable\ViewFilter;

interface ViewFilterItemInterface
{
    /**
     * Affichage.
     *
     * @return string
     */
    public function display();

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();
}