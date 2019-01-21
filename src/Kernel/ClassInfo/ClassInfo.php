<?php

namespace tiFy\Kernel\ClassInfo;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Class ClassInfo
 * @package tiFy\Components\Tools\Html
 * @see http://php.net/manual/class.reflectionclass.php
 *
 * @mixin ReflectionClass
*/
class ClassInfo
{
    /**
     * Listes des classes.
     * @var ReflectionClass[]
     */
    static $classes = [];

    /**
     * Nom de qualification de la classe courante.
     * @var string
     */
    protected $classname = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string|object Nom complet ou instance de la classe.
     *
     * @return void
     */
    public function __construct($class)
    {
        if (is_object($class)) :
            $this->classname = get_class($class);
        elseif (class_exists($class)) :
            $this->classname = $class;
        endif;

        if (!isset(self::$classes[$this->classname])) :
            try {
                self::$classes[$this->classname] = new ReflectionClass($this->classname);
            } catch (\ReflectionException $e) {
                wp_die($e->getMessage(), __('Classe indisponible', 'tify'), $e->getCode());
            }
        endif;

    }

    /**
     * Appel dynamique d'une méthode de la classe de ReflectionClass.
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (isset(self::$classes[$this->classname])) :
            try {
                return call_user_func_array([self::$classes[$this->classname], $name], $arguments);
            } catch (\ReflectionException $e) {
                wp_die($e->getMessage(), __('La méthode appelée n\'est pas disponible', 'tify'), $e->getCode());
            }
        endif;
    }

    /**
     * Récupération du chemin absolu vers le repertoire de stockage d'une application déclarée.
     *
     * @return string
     */
    public function getDirname()
    {
        return dirname($this->getFilename());
    }

    /**
     * Récupération du nom court de la classe au format kebab (Minuscules séparées par des tirets).
     *
     * @return string
     */
    public function getKebabName()
    {
        return Str::kebab($this->getShortName());
    }

    /**
     * Récupération du chemin relatif vers le repertoire de stockage d'une application déclarée.
     * @internal Basé sur le chemin absolu de la racine du proje
     * 
     * @return string
     */
    public function getRelPath()
    {
        return paths()->makeRelativePath($this->getDirname());
    }

    /**
     * Récupération de l'url vers le repertoire de stockage d'une application déclarée.
     *
     * @return string
     */
    public function getUrl()
    {
        return rtrim(url()->root($this->getRelPath()), '/');
    }
}