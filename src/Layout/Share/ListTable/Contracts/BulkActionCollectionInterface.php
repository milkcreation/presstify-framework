<?php

namespace tiFy\Layout\Share\ListTable\Contracts;

use tiFy\Layout\Share\ListTable\Contracts\BulkActionItemInterface;

interface BulkActionCollectionInterface
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Récupération de la liste des actions groupées.
     *
     * @return void|BulkActionItemInterface[]
     */
    public function all();

    /**
     * Affichage.
     *
     * @return string
     */
    public function display();

    /**
     * Traitement de la liste des actions groupées.
     *
     * @param array $bulk_actions Liste des actions groupées.
     *
     * @return void
     */
    public function parse($bulk_actions = []);
}