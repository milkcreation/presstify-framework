<?php

namespace tiFy\AdminView\Interop;

interface AttributesAwareInterface
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
     * @param array $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut.
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
     * Traitement de la liste des attributs.
     *
     * @param array $attrs Liste des attribut à traiter.
     *
     * @return void
     */
    public function parse($attrs = []);

    /**
     * Définition d'un attribut.
     *
     * @param array $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param array $value Valeur de l'attribut.
     *
     * @return void
     */
    public function set($key, $value);
}