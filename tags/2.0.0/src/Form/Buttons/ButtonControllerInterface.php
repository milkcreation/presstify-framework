<?php

namespace tiFy\Form\Buttons;

use tiFy\Form\Forms\FormItemController;

interface ButtonControllerInterface
{
    /**
     * Initialisation du controleur.
     *
     * @param string $name Nom de qualification du bouton.
     * @param Form $form Classe de rappel du formulaire associé.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function make($name, $form, $attrs = []);

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de la classe de rappel du formulaire associé.
     *
     * @return Form
     */
    public function getForm();

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'index de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés
     *
     * @return array
     */
    public function parse($attrs = []);

    /**
     * Récupération de la liste des attributs de balise HTML.
     *
     * @return array
     */
    public function getHtmlAttrs();

    /**
     * Affichage de la liste des attributs de balise HTML.
     *
     * @return string
     */
    public function displayHtmlAttrs();

    /**
     * Affichage du bouton.
     *
     * @return string
     */
    public function display();

    /**
     * Rendu d'affichage du bouton.
     *
     * @return string
     */
    public function render();
}