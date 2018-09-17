<?php

namespace tiFy\Components\Layout\ListTable\BulkAction;

use tiFy\Components\Layout\ListTable\BulkAction\BulkActionItemController;
use tiFy\Field\Field;

interface BulkActionCollectionInterface
{
    /**
     * Récupération de la liste des actions groupées.
     *
     * @return array
     */
    public function all();

    /**
     * Traitement de la liste des actions groupées.
     *
     * @param array $bulk_actions Liste des actions groupées.
     *
     * @return void
     */
    public function parse($bulk_actions = []);

    /**
     * Affichage.
     *
     * @return string
     */
    public function display();
}