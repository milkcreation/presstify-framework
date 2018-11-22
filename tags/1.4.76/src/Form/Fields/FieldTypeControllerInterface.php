<?php

namespace tiFy\Form\Fields;

interface FieldTypeControllerInterface
{
    /**
     * Initialisation du controleur.
     *
     * @param string $name Nom de qualification du type de champ.
     * @param Field $field Classe de rappel du champ associé.
     *
     * @return void
     */
    public function make($name, $field);

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
     * Traitement de la liste des options par défaut.
     *
     * @return array
     */
    public function parseDefaultOptions();

    /**
     * Traitement de la liste des attributs de balise HTML par défaut.
     *
     * @return array
     */
    public function parseDefaultHtmlAttrs();

    /**
     * Récupération de la liste des options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Récupération d'une option.
     *
     * @param string $key Clé d'index de l'option. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut
     *
     * @return mixed
     */
    public function getOption($key, $default = null);

    /**
     * Récupération de la liste des attributs de balise HTML.
     *
     * @return array
     */
    public function getHtmlAttrs();

    /**
     * Récupération d'un attribut de balise HTML.
     *
     * @param string $key Clé d'index de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut
     *
     * @return mixed
     */
    public function getHtmlAttr($key, $default = null);

    /**
     * Vérification de support d'une propriété.
     *
     * @return bool
     */
    public function support($support);

    /**
     * Affichage du champ.
     *
     * @return string
     */
    public function display();

    /**
     * Rendu de l'affichage du champ.
     *
     * @return string
     */
    public function render();
}