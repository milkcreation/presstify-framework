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
     * @param array Liste des attributs HTML.
     *
     * @return string
     */
    public function htmlAttrs($attrs);

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
     * Affiche le contenu d'un ganarit.
     *
     * @param string $name Nom de qualification.
     * @param array $data Liste des variables passées en argument.
     *
     * @return string
     */
    public function insert($name, array $data = []);

    /**
     * Définition d'une composition d'affichage (layout).
     *
     * @param string $name Nom de qualification.
     * @param array $data Liste des variables passées en argument.
     *
     * @return null
     */
    public function layout($name, array $data = []);

    /**
     * Récupération du chemin vers le template.
     *
     * @return string
     */
    public function path();

    /**
     * Ouverture de déclaration de contenu de section ajouté.
     *
     * param string $name Nom de qualification de la section.
     *
     * @return null
     */
    public function push($name);

    /**
     * Récupération de l'affichage.
     *
     * @param array $data Liste des variables passées en argument.
     *
     * @return string
     *
     * @throws \Throwable
     * @throws \Exception
     */
    public function render(array $data = []);

    /**
     * Affiche le contenu d'une section.
     *
     * @param string $name Nom de qualification.
     * @param string $default Valeur d'affichage par défaut.
     *
     * @return string|null
     */
    public function section($name, $default = null);

    /**
     * Ouverture de déclaration de contenu de section.
     *
     * @param string $name Nom de qualification.
     *
     * @return null
     */
    public function start($name);

    /**
     * Fermeture de déclaration du contenu de la section courante.
     *
     * @return null
     */
    public function stop();

    /**
     * Fetch a rendered template.
     * @param  string $name
     * @param  array  $data
     * @return string
     */
    public function fetch($name, array $data = array());

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