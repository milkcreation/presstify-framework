<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FormResolver;

interface FactoryFields extends FormResolver
{
    /**
     * Récupération de la liste des champs par ordre d'affichage.
     *
     * @return FactoryField[]
     */
    public function byGroup();

    /**
     * Récupération de la liste des champs par ordre d'affichage.
     *
     * @return FactoryField[]
     */
    public function byOrder();

    /**
     * Récupération d'un champ selon son identifiant de qualification.
     *
     * @param string $slug Identifiant de qualification.
     *
     * @return null|FactoryField
     */
    public function get($slug);

    /**
     * Vérification d'existance de groupe.
     *
     * @return bool
     */
    public function hasGroup();
}