<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Form\FormResolver;
use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FactoryForm;

interface Field extends FormResolver
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
     * Affichage.
     *
     * @return string
     */
    public function content();

    /**
     * Récupération de l'instance du contrôleur de formulaire associé.
     *
     * @return FactoryForm
     */
    public function form();

    /**
     * Liste des propriétés de support par défaut.
     *
     * @return array
     */
    public function supports();
}