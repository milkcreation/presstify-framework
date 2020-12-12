<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 */
interface ButtonDriver
{
    /**
     * Résolution de sortie de l'affichage du contrôleur.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Initialisation du contrôleur.
     *
     * @return static
     */
    public function boot(): ButtonDriver;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function build(): ButtonDriver;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de l'ordre d'affichage.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Vérification d'existance d'encapsuleur HTML.
     *
     * @return bool
     */
    public function hasWrapper(): bool;

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
     * Affichage.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): ButtonDriver;
}