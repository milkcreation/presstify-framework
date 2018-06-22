<?php

namespace tiFy\Components\AdminView\ListTable\Item;

interface ItemInterface
{
    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all();

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indice de l'attributs. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Récupération de la valeur de l'attribut de qualification de l'élément.
     *
     * @return mixed
     */
    public function getPrimary();

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indice de l'attributs. Syntaxe à point permise.
     */
    public function has($key);

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indice de l'attributs. Syntaxe à point permise.
     * @param mixed $value Valeur de de l'attribut.
     *
     * @return $this
     */
    public function set($key, $value);
}