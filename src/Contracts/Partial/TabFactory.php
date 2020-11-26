<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

use Exception;
use tiFy\Contracts\Support\ParamsBag;

interface TabFactory extends ParamsBag
{
    /**
     * Résolution de sortie la classe sous forme de chaîne de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Chargement
     *
     * @return static
     */
    public function boot(): TabFactory;

    /**
     * Initialisation.
     *
     * @return static
     *
     * @throws Exception
     */
    public function build(): TabFactory;

    /**
     * Récupération du gestionnaire d'éléments.
     *
     * @return TabCollection
     */
    public function collection(): TabCollection;

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Récupération de l'identifiant d'indexation'.
     *
     * @return int
     */
    public function getIndex(): int;

    /**
     * Récupération de la liste des éléments enfants.
     *
     * @return TabFactory[]|array
     */
    public function getChildren(): iterable;

    /**
     * Récupération du contenu d'affichage de l'élément.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Liste des attributs HTML du contenu de l'élément.
     *
     * @param bool $linearized Activation de la linéarisation de la liste des attributs HTML.
     *
     * @return string|array
     */
    public function getContentAttrs(bool $linearized = true);

    /**
     * Récupération du niveau de profondeur d'affichage de l'élément.
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
     * Liste des attributs HTML du lien de navigation de l'élément.
     *
     * @param bool $linearized Activation de la linéarisation de la liste des attributs HTML.
     *
     * @return string|array
     */
    public function getNavAttrs(bool $linearized = true);

    /**
     * Récupération du nom de qualification de l'élément parent.
     *
     * @return TabFactory
     */
    public function getParent(): ?TabFactory;

    /**
     * Récupération du nom de qualification du parent associé.
     *
     * @return string
     */
    public function getParentName(): string;

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Vérification de chargement.
     *
     * @return bool
     */
    public function isBooted(): bool;

    /**
     * Vérification d'initialisation.
     *
     * @return bool
     */
    public function isBuilt(): bool;

    /**
     * Définition du gestionnaire d'éléments.
     *
     * @param TabCollection $collection
     *
     * @return TabFactory
     */
    public function setCollection(TabCollection $collection): TabFactory;

    /**
     * Définition du niveau de profondeur dans l'interface d'affichage.
     *
     * @param int $depth
     *
     * @return static
     */
    public function setDepth(int $depth = 0): TabFactory;
}