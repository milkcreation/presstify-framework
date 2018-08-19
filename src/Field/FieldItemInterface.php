<?php

namespace tiFy\Field;

use tiFy\Field\FieldOptions\FieldOptionsCollectionController;
use tiFy\Field\FieldOptions\FieldOptionsItemController;

interface FieldItemInterface
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
     * Récupération de l'attribut de configuration de la qualification de soumission du champ "name".
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération des attributs des options de liste de sélection
     *
     * @return FieldOptionsCollectionController|FieldOptionsItemController[]
     */
    public function getOptions();

    /**
     * Récupération de l'attribut de configuration de la valeur initiale de soumission du champ "value".
     *
     * @return mixed
     */
    public function getValue();

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
     * Vérification de correspondance entre la valeur de coche et celle du champ.
     *
     * @return bool
     */
    public function isChecked();

    /**
     * Récupération de la liste des clés d'indexes des attributs de configuration.
     *
     * @return string[]
     */
    public function keys();

    /**
     * Affichage du contenu de la liste de selection
     *
     * @return void
     */
    public function options();

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