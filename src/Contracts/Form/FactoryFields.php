<?php

namespace tiFy\Contracts\Form;

use Illuminate\Support\Collection;
use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FactoryItemsIterator;
use tiFy\Contracts\Form\FactoryResolver;

interface FactoryFields extends FactoryResolver, FactoryItemsIterator
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
    public function byPosition();

    /**
     * Récupération de la liste des champs.
     *
     * @return Collection
     */
    public function collect();

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