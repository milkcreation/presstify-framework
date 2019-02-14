<?php

namespace tiFy\Template\Templates\ListTable\Contracts;

interface BulkActionsCollection
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Traitement de la liste des actions groupées.
     *
     * @param array $bulk_actions Liste des actions groupées.
     *
     * @return void
     */
    public function parse($bulk_actions = []);

    /**
     * Récupération du rendu de l'affichage.
     *
     * @return string
     */
    public function render();

    /**
     * Définition de l'emplacement d'affichage.
     *
     * @param string $which top|bottom
     *
     * @return $this
     */
    public function which($which);
}