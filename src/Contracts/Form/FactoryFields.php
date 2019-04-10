<?php

namespace tiFy\Contracts\Form;

use Illuminate\Support\Collection as IlluminateCollection;
use tiFy\Contracts\Kernel\Collection;

interface FactoryFields extends FactoryResolver, Collection
{
    /**
     * Récupération de la liste des champs par ordre d'affichage.
     *
     * @return IlluminateCollection|FactoryField[]
     */
    public function byGroup();

    /**
     * Récupération de la liste des champs par ordre d'affichage.
     *
     * @return FactoryField[]
     */
    public function byPosition();

    /**
     * Vérification d'existance de groupe.
     *
     * @return bool
     */
    public function hasGroup();
}