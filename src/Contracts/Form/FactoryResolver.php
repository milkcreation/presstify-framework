<?php

namespace tiFy\Contracts\Form;

interface FactoryResolver
{
    /**
     * Récupération de l'instance du contrôleur d'un addon associé au formulaire.
     *
     * @param string $name Nom de qualification de l'addon.
     *
     * @return AddonFactory
     */
    public function addon($name);

    /**
     * Récupération de l'instance du contrôleur des addons associés au formulaire.
     *
     * @return FactoryAddons
     */
    public function addons();

    /**
     * Récupération de l'instance du contrôleur des boutons associés au formulaire.
     *
     * @return FactoryButtons
     */
    public function buttons();

    /**
     * Récupération de l'instance du contrôleur des événements associés au formulaire ou déclenchement d'un événement.
     *
     * @param string $name Nom de qualification de l'événement.
     *
     * @return mixed|FactoryEvents
     */
    public function events($name = null);

    /**
     * Récupération de la liste des messages d'erreur.
     *
     * @return array
     */
    public function errors();

    /**
     * Récupération de l'instance du contrôleur d'un champ associé au formulaire.
     *
     * @param string $slug Identifiant de qualification du champ.
     *
     * @return FactoryField
     */
    public function field($slug);

    /**
     * Récupération de l'instance du contrôleur des champs associés au formulaire.
     *
     * @return FactoryFields
     */
    public function fields();

    /**
     * Récupération de l'instance du contrôleur de formulaire associé.
     *
     * @return FormFactory
     */
    public function form();

    /**
     * Récupération de l'instance du contrôleur des messages de notification associés au formulaire.
     *
     * @return FactoryNotices
     */
    public function notices();

    /**
     * Récupération d'une option ou de la liste complète des options du formulaire.
     *
     * @param null|string $key Clé d'indice de l'option.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function option($key = null, $default = null);

    /**
     * Récupération de l'instance du contrôleur des options associées au formulaire.
     *
     * @return FactoryOptions
     */
    public function options();

    /**
     * Récupération de l'instance du contrôleur de traitement de la requête de soumission associée au formulaire.
     *
     * @return FactoryRequest
     */
    public function request();

    /**
     * Récupération de l'instance du contrôleur de session associé au formulaire.
     *
     * @return FactorySession
     */
    public function session();
}