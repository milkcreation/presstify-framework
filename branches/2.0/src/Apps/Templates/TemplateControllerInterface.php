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

    /**
     * Assignation ou récupération de donnée(s).
     *
     * @param  array $data
     *
     * @return mixed
     */
    public function data(array $data = null);

    /**
     * Vérification d'existance d'un template.
     *
     * @return bool
     */
    public function exists();

    /**
     * Récupération du chemin vers le template.
     *
     * @return string
     */
    public function path();

    /**
     * Render the template and layout.
     * @param  array  $data
     * @throws \Throwable
     * @throws \Exception
     * @return string
     */
    public function render(array $data = array());

    /**
     * Définition du canevas d'affichage (layout) du template.
     *
     * @param string $name Nom de qualification du layout.
     * @param array $data Variables passées en argument au layout.
     *
     * @return null
     */
    public function layout($name, array $data = array());

    /**
     * Start a new section block.
     * @param  string  $name
     * @return null
     */
    public function start($name);

    /**
     * Start a new append section block.
     * @param  string $name
     * @return null
     */
    public function push($name);

    /**
     * Stop the current section block.
     * @return null
     */
    public function stop();

    /**
     * Alias of stop().
     * @return null
     */
    public function end();
    /**
     * Returns the content for a section block.
     * @param  string      $name    Section name
     * @param  string      $default Default section content
     * @return string|null
     */
    public function section($name, $default = null);

    /**
     * Fetch a rendered template.
     * @param  string $name
     * @param  array  $data
     * @return string
     */
    public function fetch($name, array $data = array());

    /**
     * Output a rendered template.
     * @param  string $name
     * @param  array  $data
     * @return null
     */
    public function insert($name, array $data = array());

    /**
     * Apply multiple functions to variable.
     * @param  mixed  $var
     * @param  string $functions
     * @return mixed
     */
    public function batch($var, $functions);

    /**
     * Escape string.
     * @param  string      $string
     * @param  null|string $functions
     * @return string
     */
    public function escape($string, $functions = null);

    /**
     * Alias to escape function.
     * @param  string      $string
     * @param  null|string $functions
     * @return string
     */
    public function e($string, $functions = null);
}