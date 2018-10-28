<?php

namespace tiFy\Contracts\Field;

interface Manager
{
    /**
     * Récupération statique du champ.
     *
     * @param string $name Nom de qualification du controleur d'affichage.
     * @param array $args Liste des variables passées en arguments.
     *
     * @return null|callable
     */
    public static function __callStatic($name, $args);

    /**
     * Récupération de l'index d'un champ déclaré.
     *
     * @param FieldController $instance Instance du champ.
     *
     * @return int
     */
    public function index(FieldController $field);

    /**
     * Récupération de l'instance d'un champ déclaré.
     *
     * @param string $name Nom de qualification de l'élément.
     * @param mixed $id Nom de qualification ou Liste des attributs de configuration.
     * @param mixed $attrs Liste des attributs de configuration.
     *
     * @return FieldController
     */
    public function get($name, $id = null, $attrs = null);

    /**
     * Déclaration d'un contrôleur de champ.
     *
     * @param string $name Nom de qualification
     * @param string $concrete Nom de qualification du controleur.
     *
     * @return boolean
     */
    public function register($name, $concrete);
}