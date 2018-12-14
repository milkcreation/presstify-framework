<?php

namespace tiFy\View\Pattern\ListTable\Contracts;

interface RowActionsCollection
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Récupération de la liste des actions par ligne.
     *
     * @return array
     */
    public function all();

    /**
     * Affichage.
     *
     * @return string
     */
    public function display();

    /**
     * Traitement de la liste des actions par ligne.
     *
     * @param array $row_actions Liste des actions par ligne.
     *
     * @return void
     */
    public function parse($row_actions = []);
}