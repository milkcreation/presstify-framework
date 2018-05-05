<?php

namespace tiFy\Forms\Buttons;

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
     * Récupération du nom de qualification du formulaire
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de la classe de rappel du formulaire associé.
     *
     * @return Form
     */
    public function form();

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
     * Liste des classes HTML du bouton
     *
     * @return array
     */
    public function getClasses();

    /**
     * Affichage du bouton
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