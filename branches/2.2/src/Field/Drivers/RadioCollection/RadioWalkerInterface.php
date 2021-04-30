<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers\RadioCollection;

use tiFy\Field\Drivers\RadioCollectionDriverInterface;

interface RadioWalkerInterface
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
    public function build(): RadioWalkerInterface;

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
    public function registerChecked(): RadioWalkerInterface;

    /**
     * Récupération du rendu d'affichage de l'élément.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition du controleur de champ associé.
     *
     * @param RadioCollectionDriverInterface $field
     *
     * @return static
     */
    public function setField(RadioCollectionDriverInterface $field): RadioWalkerInterface;

    /**
     * Définition d'un élément.
     *
     * @param RadioChoiceInterface|array $item
     * @param string|int $key
     *
     * @return RadioChoiceInterface
     */
    public function setItem($item, $key = null): RadioChoiceInterface;
}