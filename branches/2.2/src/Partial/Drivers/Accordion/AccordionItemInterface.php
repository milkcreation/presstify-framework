<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\Accordion;

use tiFy\Contracts\Support\ParamsBag;

interface AccordionItemInterface extends ParamsBag
{
    /**
     * Résolution de sortie de la classe sous forme d'une chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function build(): AccordionItemInterface;

    /**
     * Récupération du niveau de profondeur de l'élément.
     *
     * @return int
     */
    public function getDepth(): int;

    /**
     * Récupération du nom de qualification de l'élément.
     *
     * @return string|int
     */
    public function getId();

    /**
     * Récupération du nom de qualification du parent.
     *
     * @return string|null
     */
    public function getParent(): ?string;

    /**
     * Vérification de l'état d'ouverture de l'élément.
     *
     * @return bool
     */
    public function isOpened(): bool;

    /**
     * Affichage de l'élément.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition de l'instance du gestionnaire d'affichage de la liste des éléments.
     *
     * @param AccordionCollectionInterface $collection
     *
     * @return static
     */
    public function setCollection(AccordionCollectionInterface $collection): AccordionItemInterface;

    /**
     * Définition du niveau de profondeur de l'élément.
     *
     * @param int $depth
     *
     * @return static
     */
    public function setDepth(int $depth): AccordionItemInterface;

    /**
     * Définition de l'état d'ouverture de l'élément.
     *
     * @param bool $open
     *
     * @return static
     */
    public function setOpened(bool $open = true): AccordionItemInterface;
}
