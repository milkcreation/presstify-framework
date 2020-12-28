<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers\CheckboxCollection;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Field\Drivers\CheckboxDriverInterface;
use tiFy\Field\Drivers\LabelDriverInterface;

interface CheckboxChoiceInterface extends ParamsBag
{
    /**
     * Résolution de sortie de la classe sous la forme d'une chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function build(): CheckboxChoiceInterface;

    /**
     * Récupération de l'intance de la checkbox.
     *
     * @return CheckboxDriverInterface
     */
    public function getCheckbox(): CheckboxDriverInterface;

    /**
     * Récupération du l'identifiant de qualification de l'élément.
     *
     * @return string|int
     */
    public function getId();

    /**
     * Récupération de l'intance du label.
     *
     * @return LabelDriverInterface
     */
    public function getLabel(): LabelDriverInterface;

    /**
     * Récupération du nom de soumission de la requête de traitement.
     *
     * @return string
     */
    public function getNameAttr(): string;

    /**
     * Récupération de la valeur de soumission de la requête de traitement.
     *
     * @return mixed|null
     */
    public function getValue();

    /**
     * Vérification de l'indicateur de selection de l'élément.
     *
     * @return bool
     */
    public function isChecked(): bool;

    /**
     * Récupération du rendu d'affichage de l'élément.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition du nom de soumission de la requête de traitement.
     *
     * @param string $name
     *
     * @return static
     */
    public function setNameAttr(string $name): CheckboxChoiceInterface;

    /**
     * Définition de la selection de l'élément pour la requête de traitement.
     *
     * @return static
     */
    public function setChecked(): CheckboxChoiceInterface;

    /**
     * Définition de l'instance du gestionnaire d'affichage de la liste des éléments.
     *
     * @param CheckboxWalkerInterface $walker
     *
     * @return static
     */
    public function setWalker(CheckboxWalkerInterface $walker): CheckboxChoiceInterface;
}