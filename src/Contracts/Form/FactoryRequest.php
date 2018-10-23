<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Form\FactoryResolver;
use tiFy\Contracts\Kernel\ParamsBagInterface;

interface FactoryRequest extends FactoryResolver, ParamsBagInterface
{
    /**
     * Traitement de la requête de soumission du formulaire.
     *
     * @return bool|void
     */
    public function handle();

    /**
     * Préparation des données de traitement de la requête.
     *
     * @return void
     */
    public function prepare();
}