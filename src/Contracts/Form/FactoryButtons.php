<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Form\ButtonController;
use tiFy\Contracts\Form\FactoryItemsIterator;
use tiFy\Contracts\Form\FactoryResolver;

interface FactoryButtons extends FactoryResolver, FactoryItemsIterator
{
    /**
     * Récupération de la liste des éléments par ordre d'affichage.
     *
     * @return ButtonController[]
     */
    public function byPosition();

    /**
     * Récupération de l'instance du contrôleur d'un bouton.
     *
     * @param string $name Nom de qualification.
     *
     * @return null|ButtonController
     */
    public function get($name);
}