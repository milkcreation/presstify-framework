<?php

namespace tiFy\Partial;

use tiFy\Field\FieldOptions\FieldOptionsCollectionController;
use tiFy\Field\FieldOptions\FieldOptionsItemController;

interface PartialItemInterface
{
    /**
     * Affichage du contenu placé après le champ
     *
     * @return void
     */
    public function after();

    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all();

    /**
     * Affichage de la liste des attributs de balise.
     *
     * @return string
     */
    public function attrs();

    /**
     * Affichage du contenu placé avant le champ
     *
     * @return void
     */
    public function before();

    /**
     * Récupération une liste d'attributs de configuration.
     *
     * @param string[] $keys Clé d'index des attributs à retourner.
     *
     * @return array
     */
    public function compact($keys = []);

    /**
     * Affichage du contenu de la balise champ.
     *
     * @return void
     */
    public function content();

    /**
     * Affichage.
     *
     * @return string
     */
    public function display();

    /**
     * Mise en file des scripts CSS et JS utilisés pour l'affichage.
     *
     * @return $this
     */
    public function enqueue_scripts();

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Récupération d'un attribut de balise HTML.
     *
     * @param string $key Clé d'indexe de l'attribut.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function getAttr($key, $default = '');

    /**
     * Récupération de la liste des attributs HTML.
     *
     * @return array
     */
    public function getAttrs();

    /**
     * Récupération de l'identifiant de qualification du controleur.
     *
     * @return string
     */
    public function getId();

    /**
     * Récupération de l'indice de la classe courante.
     *
     * @return int
     */
    public function getIndex();

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return bool
     */
    public function has($key);

    /**
     * Vérification d'existance d'un attribut de balise HTML.
     *
     * @param string $key Clé d'indexe de l'attribut.
     *
     * @return string
     */
    public function hasAttr($key);

    /**
     * Vérifie si une variable peut être appelée en tant que fonction.
     *
     * @return bool
     */
    public function isCallable($var);

    /**
     * Récupération de la liste des clés d'indexes des attributs de configuration.
     *
     * @return string[]
     */
    public function keys();

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return void
     */
    public function parse($attrs = []);

    /**
     * Récupére la valeur d'un attribut avant de le supprimer.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut lorsque l'attribut n'est pas défini.
     *
     * @return mixed
     */
    public function pull($key, $default = null);

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return $this
     */
    public function set($key, $value);

    /**
     * Définition d'un attribut de balise HTML
     *
     * @param string $key Clé d'indexe de l'attribut.
     * @param null|mixed $value Valeur de l'attribut.
     *
     * @return $this
     */
    public function setAttr($key, $value = null);

    /**
     * Récupération de la liste des valeurs des attributs de configuration.
     *
     * @return mixed[]
     */
    public function values();
}