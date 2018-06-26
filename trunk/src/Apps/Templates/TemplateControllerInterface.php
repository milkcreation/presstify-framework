<?php

namespace tiFy\Apps\Templates;

interface TemplateControllerInterface
{
    /**
     * Récupération de la liste complète des attributs de configuration.
     *
     * @return array
     */
    public function all();

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = '');

    /**
     * Récupération de l'une variable passée en argument dans le controleur.
     *
     * @return string
     */
    public function getArg($key, $default = null);

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return bool
     */
    public function has($key);

    /**
     * Linéarisation d'une liste d'attributs HTML.
     *
     * @return string
     */
    public function htmlAttrs($attrs);

    /**
     * Affichage d'un template frère.
     *
     * @return null
     */
    public function partial($name, $datas = []);
}