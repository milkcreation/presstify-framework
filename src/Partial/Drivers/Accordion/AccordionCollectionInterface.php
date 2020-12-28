<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\Accordion;

use tiFy\Partial\Drivers\AccordionDriverInterface;

interface AccordionCollectionInterface
{
    /**
     * Résolution de sortie de l'affichage du contrôleur.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function build(): AccordionCollectionInterface;

    /**
     * Vérification d'existance d'éléments.
     *
     * @return bool
     */
    public function exists(): bool;

    /**
     * Déclaration de la liste des éléments ouverts à l'initialisation.
     *
     *
     * @return static
     */
    public function registerOpened(): AccordionCollectionInterface;

    /**
     * Récupération du rendu d'affichage.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition d'un élément.
     *
     * @param AccordionItemInterface|array $item
     * @param string|int $key
     *
     * @return AccordionItemInterface
     */
    public function setItem($item, $key = null): AccordionItemInterface;

    /**
     * Définition du parent initial.
     *
     * @param string|null $parent
     *
     * @return static
     */
    public function setParent(?string $parent = null): AccordionCollectionInterface;

    /**
     * Définition du controleur d'affichage associé.
     *
     * @param AccordionDriverInterface $partial Contrôleur d'affichage associé.
     *
     * @return static
     */
    public function setPartial(AccordionDriverInterface $partial): AccordionCollectionInterface;

    /**
     * Itération d'affichage la liste des éléments.
     *
     * @param AccordionItem[] $items Liste des éléments.
     * @param int $depth Niveau de profondeur courant de l'itération.
     * @param string|null $parent Parent courant de l'itération.
     *
     * @return string
     */
    public function walk($items = [], $depth = 0, ?string $parent = null): string;
}
