<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers\Select;

use tiFy\Contracts\Support\ParamsBag;

interface SelectChoiceInterface extends ParamsBag
{
    /**
     * Récupération du contenu de la balise.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de la valeur.
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * Récupération du groupe parent.
     *
     * @return string|null
     */
    public function getParent(): ?string;

    /**
     * Vérification d'existance d'un groupe parent.
     *
     * @return bool
     */
    public function hasParent(): bool;

    /**
     * Vérifie si l'option est désactivée.
     *
     * @return bool
     */
    public function isDisabled(): bool;

    /**
     * Vérifie si l'option est un groupe.
     *
     * @return bool
     */
    public function isGroup(): bool;

    /**
     * Vérifie si l'option est sélectionnée.
     *
     * @return bool
     */
    public function isSelected(): bool;

    /**
     * Définition du niveau de profondeur.
     *
     * @param int $depth
     *
     * @return static
     */
    public function setDepth(int $depth = 0): SelectChoiceInterface;

    /**
     * Définition de la selection
     *
     * @param array $selected
     *
     * @return static
     */
    public function setSelected(array $selected): SelectChoiceInterface;

    /**
     * Balise de fermeture.
     *
     * @return string
     */
    public function tagClose(): string;

    /**
     * Contenu de la balise.
     *
     * @return string
     */
    public function tagContent(): string;

    /**
     * Balise d'ouverture.
     *
     * @return string
     */
    public function tagOpen(): string;
}