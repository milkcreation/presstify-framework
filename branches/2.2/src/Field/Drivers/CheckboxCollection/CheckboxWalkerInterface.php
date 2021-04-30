<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers\CheckboxCollection;

use tiFy\Field\Drivers\CheckboxCollectionDriverInterface;

interface CheckboxWalkerInterface
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Initialisation
     *
     * @return static
     */
    public function build(): CheckboxWalkerInterface;

    /**
     * Vérification d'existance d'éléments.
     *
     * @return bool
     */
    public function exists(): bool;

    /**
     * Déclaration des éléments sélectionnés.
     *
     * @return static
     */
    public function registerChecked(): CheckboxWalkerInterface;

    /**
     * Récupération du rendu d'affichage de l'élément.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition du controleur de champ associé.
     *
     * @param CheckboxCollectionDriverInterface $field
     *
     * @return static
     */
    public function setField(CheckboxCollectionDriverInterface $field): CheckboxWalkerInterface;

    /**
     * Définition d'un élément.
     *
     * @param CheckboxChoiceInterface|array $item
     * @param string|int $key
     *
     * @return CheckboxChoiceInterface
     */
    public function setItem($item, $key = null): CheckboxChoiceInterface;
}