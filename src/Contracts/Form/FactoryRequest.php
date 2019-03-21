<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Support\ParamsBag;

interface FactoryRequest extends FactoryResolver, ParamsBag
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

    /**
     * Réinitialisation des champs.
     *
     * @return void
     */
    public function resetFields();
}