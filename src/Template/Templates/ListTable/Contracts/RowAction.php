<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\Template\FactoryAwareTrait;

interface RowAction extends FactoryAwareTrait, ParamsBag
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Récupération de l'identifiant de qualification de la clef de sécurisation d'une action sur un élément.
     *
     * @return string
     */
    public function getNonce(): string;

    /**
     * Vérification d'activation de l'action.
     *
     * @return boolean
     */
    public function isActive(): bool;

    /**
     * Récupération du rendu de l'affichage.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition du nom de qualification.
     *
     * @param string $name Nom de qualification.
     *
     * @return static
     */
    public function setName(string $name): RowAction;
}