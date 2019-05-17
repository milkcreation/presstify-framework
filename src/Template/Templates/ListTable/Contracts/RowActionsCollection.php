<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\Collection;

interface RowActionsCollection extends Collection
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString(): string;

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
     * @return static
     */
    public function parse(array $row_actions = []): RowActionsCollection;

    /**
     * Récupération du rendu de l'affichage.
     *
     * @return string
     */
    public function render(): string;
}