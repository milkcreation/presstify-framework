<?php

namespace tiFy\Components\AdminView\ListTable\RowAction;

interface RowActionItemInterface
{
    /**
     * Récupération de l'identifiant de qualification de la clef de sécurisation d'une action sur un élément.
     *
     * @return string
     */
    public function getNonce();

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