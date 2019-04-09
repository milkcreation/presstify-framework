<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\Collection;

interface BulkActionsCollection extends Collection
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Traitement de la liste des actions groupées.
     *
     * @param array $bulk_actions Liste des actions groupées.
     *
     * @return static
     */
    public function parse(array $bulk_actions = []): BulkActionsCollection;

    /**
     * Récupération du rendu de l'affichage.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition de l'emplacement d'affichage.
     *
     * @param string $which top|bottom
     *
     * @return $this
     */
    public function which(string $which): BulkActionsCollection;
}