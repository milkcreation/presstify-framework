<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\Form\FormFactory;

interface FactoryView extends ViewController
{
    /**
     * Translation d'appel des méthodes de l'application associée.
     *
     * @param string $name Nom de la méthode à appeler.
     * @param array $arguments Liste des variables passées en argument.
     *
     * @return mixed
     */
    public function __call($name, $arguments);

    /**
     * Post-affichage.
     *
     * @return string
     */
    public function after();

    /**
     * Récupération des attributs de formulaires linéarisés.
     *
     * @return string
     */
    public function attrs();

    /**
     * Pré-affichage.
     *
     * @return string
     */
    public function before();

    /**
     * Récupération de l'instance du contrôleur de formulaire.
     *
     * @return FormFactory
     */
    public function form();

    /**
     * Traitement et récupération d'une liste d'attributs HTML.
     *
     * @param array $attrs Liste des attributs HTML.
     * @param bool $linearized Activation de la linéarisation.
     *
     * @return string
     */
    public function getHtmlAttrs($attrs = [], $linearized = true);
}