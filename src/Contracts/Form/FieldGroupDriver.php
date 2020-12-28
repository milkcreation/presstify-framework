<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

use Illuminate\Support\Collection;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 */
interface FieldGroupDriver
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): FieldGroupDriver;

    /**
     * Post-affichage.
     *
     * @return string
     */
    public function after(): string;

    /**
     * Pré-affichage.
     *
     * @return string
     */
    public function before(): string;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de la liste des attributs de balise HTML.
     *
     * @param bool $linearized Linératisation des valeurs.
     *
     * @return string|array
     */
    public function getAttrs(bool $linearized = true);

    /**
     * Récupération de la liste des champs associé au groupe.
     *
     * @return Collection|FieldDriver[]|array
     */
    public function getFields(): iterable;

    /**
     * Récupération du groupe parent
     *
     * @return FieldGroupDriver|null
     */
    public function getParent(): ?FieldGroupDriver;

    /**
     * Récupération du positionnement de l'élément.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Instance du gestionnaire de groupe de champs.
     *
     * @return FieldGroupsFactory|null
     */
    public function groupsManager(): ?FieldGroupsFactory;

    /**
     * Définition de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): FieldGroupDriver;

    /**
     * Définition du gestionnaire de groupes de champs.
     *
     * @param FieldGroupsFactory $groupsManager
     *
     * @return static
     */
    public function setGroupManager(FieldGroupsFactory $groupsManager): FieldGroupDriver;
}