<?php

namespace tiFy\Components\Tools\ClassInfo;

use Illuminate\Support\Arr;
use ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;
use tiFy\tiFy;

/**
 * Class ClassInfo
 * @package tiFy\Components\Tools\Html
 * @see http://php.net/manual/class.reflectionclass.php
 *
 * @method mixed getConstant(string $name) — Récupère une constante
 * @method array getConstants() — Récupère les constantes
 * @method ReflectionMethod getConstructor() — Récupère le constructeur d'une classe
 * @method array getDefaultProperties() — Récupère les propriétés par défaut
 * @method string getDocComment() — Récupère les commentaires
 * @method int getEndLine() — Récupère la fin d'une ligne
 * @method ReflectionExtension getExtension() — Récupère un objet ReflectionExtension pour l'extension définissant la classe
 * @method string getExtensionName() — Récupère le nom de l'extension qui définit la classe
 * @method string getFileName() — Récupère le nom du fichier déclarant la classe considérée
 * @method array getInterfaceNames() — Récupère les noms des interfaces
 * @method array getInterfaces() — Récupère les interfaces
 * @method ReflectionMethod getMethod(string $name) — Récupère un objet ReflectionMethod pour une méthode d'une classe
 * @method array getMethods(null|int $filter = null) — Récupère un tableau de méthodes
 * @method int getModifiers() — Récupère les modificateurs de classe
 * @method string getName() — Récupère le nom de la classe
 * @method string getNamespaceName() — Récupère l'espace de noms
 * @method ReflectionClass getParentClass() — Récupère la classe parente
 * @method array getProperties(null|int $filter = null) — Récupère les propriétés
 * @method ReflectionProperty getProperty(string $name) — Récupère une ReflectionProperty pour une propriété d'une classe
 * @method ReflectionClassConstant getReflectionConstant(string $name) — Gets a ReflectionClassConstant for a class's constant
 * @method array getReflectionConstants() — Gets class constants
 * @method string getShortName() — Récupère le nom court d'une classe
 * @method int getStartLine() — Récupère le numéro de ligne de départ
 * @method array getStaticProperties() — Récupère les propriétés statiques
 * @method mixed getStaticPropertyValue(string $name , mixed &$def_value = null) — Récupère la valeur d'une propriété statique
 * @method array getTraitAliases() — Retourne un tableau des alias du trait
 * @method array getTraitNames() — Retourne un tableau de noms des traits utilisés par cette classe
 * @method array getTraits() — Retourne un tableau des traits utilisés par cette classe
 * @method bool hasConstant(string $name) — Vérifie si une constante est définie
 * @method bool hasMethod(string $name) — Vérifie si une méthode est définie
 * @method bool hasProperty(string $name) — Vérifie si une propriété est définie
 * @method bool implementsInterface(string $interface) — Vérifie si une classe implémente une interface
 * @method bool inNamespace() — Vérifie si une classe est définie dans un espace de noms
 * @method bool isAbstract() — Vérifie si une classe est abstraite
 * @method bool isAnonymous() — Checks if class is anonymous
 * @method bool isCloneable() — Renseigne à propos de la propriété de duplication de la classe
 * @method bool isFinal() — Vérifie si une classe est finale
 * @method bool isInstance(object $object) — Vérifie si une classe est une instance d'une autre classe
 * @method bool isInstantiable() — Vérifie si une classe est instanciable
 * @method bool isInterface() — Vérifie si une classe est une interface
 * @method bool isInternal() — Vérifie si une classe est définie comme interne par une extension
 * @method bool isIterable() — Check whether this class is iterable
 * @method bool isIterateable() — Vérifie si la classe est itérable
 * @method bool isSubclassOf(string $class) — Vérifie si la classe est une sous-classe
 * @method bool isTrait() — Renseigne s'il s'agit d'un trait
 * @method bool isUserDefined() — Vérifie si une classe a été définie dans l'espace utilisateur
 * @method object newInstance(mixed $args , ...$mixed) — Créer une nouvelle instance de la classe en utilisant les arguments fournis
 * @method object newInstanceArgs(array $args) — Créer une nouvelle instance en utilisant les arguments fournis
 * @method object newInstanceWithoutConstructor() — Crée une nouvelle instance de la classe sans invoquer le constructeur
 * @method void setStaticPropertyValue(string $name, string $value) — Définit la valeur d'une propriété statiques
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
        elseif (class_exists($app)) :
            $this->classname = $class;
        endif;

        if (!isset(self::$classes[$this->classname])) :
            try {
                self::$classes[$this->classname] = new ReflectionClass($this->classname);
            } catch (ReflectionException $e) {
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
            } catch (ReflectionException $e) {
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
     * Récupération du chemin relatif vers le repertoire de stockage d'une application déclarée.
     * @internal Basé sur le chemin absolu de la racine du proje
     * 
     * @return string
     */
    public function getRelPath()
    {
        return (new fileSystem())->makePathRelative($this->getDirname(), tiFy::instance()->absPath());
    }

    /**
     * Récupération de l'url vers le repertoire de stockage d'une application déclarée.
     *
     * @return string
     */
    public function getUrl()
    {
        return rtrim(\home_url($this->getRelPath()), '/');
    }
}