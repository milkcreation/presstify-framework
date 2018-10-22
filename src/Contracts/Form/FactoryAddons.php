<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Form\AddonFactory;
use tiFy\Contracts\Form\FactoryResolver;

interface FactoryAddons extends FactoryResolver
{
    /**
     * Récupération de l'instance du contrôleur d'un addon.
     *
     * @param string $name Nom de qualification.
     *
     * @return null|AddonFactory
     */
    public function get($name);
}