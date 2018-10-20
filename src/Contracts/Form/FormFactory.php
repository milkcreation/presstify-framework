<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Form\FormResolver;
use tiFy\Contracts\Form\FormView;
use tiFy\Contracts\Kernel\ParamsBagInterface;
use tiFy\Contracts\Views\ViewsInterface;


interface FormFactory extends FormResolver, ParamsBagInterface
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
     * Récupération du nom de qualification du formulaire.
     *
     * @return string
     */
    public function name();

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