<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Option;

use Pollen\Support\ParamsBagInterface;
use tiFy\Contracts\View\Engine as ViewEngine;

interface OptionPageInterface extends ParamsBagInterface
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Récupération de l'identificant de qualification d'accroche.
     *
     * @return string
     */
    public function getHookname(): string;

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Vérifie si la page est un sous élement du menu "Réglages" de Wordpress.
     *
     * @return bool
     */
    public function isSettingsPage(): bool;

    /**
     * Déclaration des options associées à la page.
     *
     * @param array|string[] $settings
     *
     * @return static
     */
    public function registerSettings(array $settings): OptionPageInterface;

    /**
     * Affichage.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition du gestionnaire d'options.
     *
     * @param OptionInterface $manager Instance du gestionnaire d'options.
     *
     * @return static
     */
    public function setManager(OptionInterface $manager): OptionPageInterface;

    /**
     * Définition du nom de qualification.
     *
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name): OptionPageInterface;

    /**
     * Récupération de la vue.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'intance du controleur principal.}
     * {@internal Sinon récupére le gabarit d'affichage et passe les variables en argument.}
     *
     * @param null|string $name
     * @param array $datas
     *
     * @return ViewEngine|string
     */
    public function view(?string $name= null, array $datas = []);
}