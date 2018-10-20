<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Form\ButtonFactory;
use tiFy\Contracts\Form\FormResolver;

interface FactoryButtons extends FormResolver
{
    /**
     * Récupération de l'instance du contrôleur d'un bouton.
     *
     * @param string $name Nom de qualification.
     *
     * @return null|ButtonFactory
     */
    public function get($name);
}