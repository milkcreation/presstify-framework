<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 */
interface AddonDriver
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): AddonDriver;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function build(): AddonDriver;

    /**
     * Liste des attributs de configuration par défaut du formulaire associé.
     *
     * @return array
     */
    public function defaultFormOptions(): array;

    /**
     * Liste des attributs de configuration par défaut des champs du formulaire associé.
     *
     * @return array
     */
    public function defaultFieldOptions(): array;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string;

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
     * Définition de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): AddonDriver;
}