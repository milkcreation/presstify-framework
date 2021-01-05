<?php

declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\Contracts\View\PlatesEngine;

/**
 * @mixin \tiFy\Support\Concerns\BootableTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 * @mixin MetaboxAwareTrait
 */
interface MetaboxContextInterface
{
    /**
     * Résolution de sortie de la classe sous forme de chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): MetaboxContextInterface;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de la liste des pilotes associés.
     *
     * @return MetaboxDriverInterface[]|array
     */
    public function getDrivers(): array;

    /**
     * Récupération du rendu d'affichage du contexte.
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
    public function setAlias(string $alias): MetaboxContextInterface;

    /**
     * Définition d'un pilote associé.
     *
     * @param MetaboxDriverInterface $driver
     *
     * @return static
     */
    public function setDriver(MetaboxDriverInterface $driver): MetaboxContextInterface;

    /**
     * Définition de l'écran associé.
     *
     * @param MetaboxScreenInterface $screen
     *
     * @return static
     */
    public function setScreen(MetaboxScreenInterface $screen): MetaboxContextInterface;

    /**
     * Récupération d'un instance du controleur de liste des gabarits d'affichage ou d'un gabarit d'affichage.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'instance du controleur de liste.}
     * {@internal Sinon récupére l'instance du gabarit d'affichage et passe les variables en argument.}
     *
     * @param string|null view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return PlatesEngine|string
     */
    public function view(?string $view = null, array $data = []);
}