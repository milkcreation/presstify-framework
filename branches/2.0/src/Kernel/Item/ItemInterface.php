<?php

namespace tiFy\Kernel\Item;

interface ItemInterface
{
    /**
     * Récupération de la liste des attributs.
     *
     * @return array
     */
    public function all();

    /**
     * Définition de la liste des attributs par défaut.
     *
     * @return array
     */
    public function defaults();

    /**
     * Récupération d'un attribut.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut lorsque l'attribut n'est pas défini.
     *
     * @return mixed
     */
    public function get($key, $default = '');

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return mixed
     */
    public function has($key, $default = null);

    /**
     * Récupération de la liste des clés d'indexes des attributs de configuration.
     *
     * @return string[]
     */
    public function keys();

    /**
     * Traitement de la liste des attributs.
     *
     * @param array $attrs Liste des attribut à traiter.
     *
     * @return void
     */
    public function parse($attrs = []);

    /**
     * Ajout d'un attribut au début de la liste des attributs.
     *
     * @param  mixed $value Valeur à ajouter
     * @param  string $key $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return array
     */
    public function unshift($value, $key);

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
     * Définition d'un attribut.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return void
     */
    public function set($key, $value);

    /**
     * Récupération de la liste des valeurs des attributs de configuration.
     *
     * @return mixed[]
     */
    public function values();
}