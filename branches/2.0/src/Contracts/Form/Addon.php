<?php

namespace tiFy\Contracts\Form;

interface Addon
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Récupération d'une option de configuration d'un champ associé.
     *
     * @param Field $field Classe de rappel du controleur de champ associé.
     * @param string $key Clé d'index de l'option à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getFieldOption($field, $key, $default = '');

    /**
     * Récupération d'une option de configuration du formulaire associé.
     *
     * @param string $key Clé d'index de l'option à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getFormOption($key, $default = null);

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Traitement de la liste des méthodes de court-circuitage de traitement du formulaire.
     *
     * @return void
     */
    public function parseCallbacks();

    /**
     * Traitement des attributs de configuration par défaut d'un champ.
     *
     * @param Field $field Classe de rappel de controleur du champ.
     *
     * @return void
     */
    public function parseDefaultFieldOptions($field);

    /**
     * Traitement de la liste des options de formulaire par défaut.
     *
     * @return array
     */
    public function parseDefaultFormOptions();
}