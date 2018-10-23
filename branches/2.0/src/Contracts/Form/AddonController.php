<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Kernel\ParamsBagInterface;

interface AddonController extends ParamsBagInterface
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Liste des attributs de configuration par défaut des champs du formulaire associé.
     *
     * @return array
     */
    public function defaultFieldOptions();

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();
}