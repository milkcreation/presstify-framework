<?php

namespace tiFy\Components\AdminView\ListTable\RowAction;

interface RowActionCollectionInterface
{
    /**
     * Récupération de la liste des actions par ligne.
     *
     * @return array
     */
    public function all();

    /**
     * Traitement de la liste des actions par ligne.
     *
     * @param array $row_actions Liste des actions par ligne.
     *
     * @return void
     */
    public function parse($row_actions = []);

    /**
     * Affichage.
     *
     * @return string
     */
    public function display();
}