<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\CurtainMenu;

use tiFy\Contracts\Support\ParamsBag;

interface CurtainMenuItemInterface extends ParamsBag
{
    /**
     * Vérification d'existance d'éléments enfants associés.
     *
     * @return bool
     */
    public function hasChild(): bool;

    /**
     * Vérification d'existance d'un élément parent associé.
     *
     * @return bool
     */
    public function hasParent(): bool;

    /**
     * Récupération de la liste des attributs HTML.
     *
     * @param bool $linearized Linéarisation de la liste des attributs HTML.
     *
     * @return string|array
     */
    public function getAttrs(bool $linearized = true);

    /**
     * Récupération de la liste des attributs du bouton de retour.
     *
     * @return array
     */
    public function getBack(): array;

    /**
     * Récupération de la liste des éléments associés (enfants).
     *
     * @return CurtainMenuItem[]|null
     */
    public function getChildren(): ?array;

    /**
     * Récupération du niveau de profondeur de l'élément.
     *
     * @return int
     */
    public function getDepth(): int;

    /**
     * Récupération du nom de qualification de l'élément.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de la liste des attributs de l'interface de navigation vers le panneau de l'élément.
     *
     * @return array
     */
    public function getNav(): array;

    /**
     * Récupération de l'élément parent.
     *
     * @return CurtainMenuItemInterface|null
     */
    public function getParent(): ?CurtainMenuItemInterface;

    /**
     * Récupération du nom de qualification de l'élément parent.
     *
     * @return string
     */
    public function getParentName(): ?string;

    /**
     * Récupération de la liste des attributs de l'intitulé de qualification.
     *
     * @return array
     */
    public function getTitle(): array;

    /**
     * Vérification de sélection de l'élément.
     *
     * @return bool
     */
    public function isSelected(): bool;

    /**
     * Définition du niveau de profondeur de l'élément.
     *
     * @param int $depth
     *
     * @return static
     */
    public function setDepth(int $depth): CurtainMenuItemInterface;

    /**
     * Définition de l'instance du gestionnaire d'éléments associé.
     *
     * @param CurtainMenuCollectionInterface $manager
     *
     * @return static
     */
    public function setManager(CurtainMenuCollectionInterface $manager): CurtainMenuItemInterface;

    /**
     * Définition de la sélection de l'élément.
     *
     * @param bool $selected
     *
     * @return static
     */
    public function setSelected($selected = false): CurtainMenuItemInterface;

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function parse(): CurtainMenuItemInterface;
}