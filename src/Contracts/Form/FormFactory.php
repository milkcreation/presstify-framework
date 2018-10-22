<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Form\FactoryResolver;
use tiFy\Contracts\Form\FormView;
use tiFy\Contracts\Kernel\ParamsBagInterface;
use tiFy\Contracts\Views\ViewsInterface;


interface FormFactory extends FactoryResolver, ParamsBagInterface
{
    /**
     * Résolution de sortie de l'affichage.
     *
     * @return string
     */
    public function __toString();

    /**
     * Initialisation du contrôleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Récupération de l'instance du contrôleur d'affichage du formulaire.
     *
     * @return FactoryDisplay
     */
    public function display();

    /**
     * Récupération de l'action du formulaire (url).
     *
     * @return string
     */
    public function getAction();

    /**
     * Récupération de la clé d'indexe de sécurisation du formulaire (CSRF).
     *
     * @return string
     */
    public function getCsrf();

    /**
     * Récupération de la méthode de soumission du formulaire.
     *
     * @return string
     */
    public function getMethod();

    /**
     * Récupération de l'intitulé de qualification du formulaire.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Récupération du nom de qualification du formulaire.
     *
     * @return string
     */
    public function name();

    /**
     * Evénement de déclenchement à l'initialisation du formulaire en tant que formulaire courant.
     *
     * @return void
     */
    public function onSetCurrent();

    /**
     * Evénement de déclenchement à la réinitialisation du formulaire courant du formulaire.
     *
     * @return void
     */
    public function onResetCurrent();

    /**
     * Récupération d'un instance du controleur de liste des gabarits d'affichage ou d'un gabarit d'affichage.
     * {@internal
     *  - cas 1 : Aucun argument n'est passé à la méthode, retourne l'instance du controleur de gabarit d'affichage.
     *  - cas 2 : Rétourne le gabarit d'affichage en passant les variables en argument.
     * }
     *
     * @param null|string $view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewsInterface|FormView
     */
    public function viewer($view, $data = []);
}