<?php

namespace tiFy\Contracts\Partial;

interface PartialManager
{
    /**
     * Récupération statique d'un élément.
     *
     * @param string $name Nom de qualification.
     * @param array $args Liste des variables passées en arguments.
     *
     * @return null|PartialController
     */
    public static function __callStatic($name, $args);

    /**
     * Récupération de l'instance d'un élément déclaré.
     *
     * @param string $name Nom de qualification de l'élément.
     * @param mixed $id Nom de qualification ou Liste des attributs de configuration.
     * @param mixed $attrs Liste des attributs de configuration.
     *
     * @return null|PartialController
     */
    public function get($name, $id = null, $attrs = null);

    /**
     * Récupération de l'index d'un contrôleur d'affichage déclaré.
     *
     * @param PartialController $partial Instance du contrôleur de champ.
     *
     * @return int
     */
    public function index(PartialController $partial);

    /**
     * Déclaration d'un controleur d'affichage.
     *
     * @param string $name Nom de qualification d"appel de l'élément.
     * @param string $concrete Nom de qualification du controleur.
     *
     * @return boolean
     */
    public function register($name, $concrete);
}