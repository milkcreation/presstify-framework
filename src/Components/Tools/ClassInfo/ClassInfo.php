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
 * @method ReflectionClass getConstant(string $name) — Récupère une constante
 * @method ReflectionClass getConstants() — Récupère les constantes
 * @method ReflectionClass getConstructor() — Récupère le constructeur d'une classe
 * @method ReflectionClass getDefaultProperties() — Récupère les propriétés par défaut
 * @method ReflectionClass getDocComment() — Récupère les commentaires
 * @method ReflectionClass getEndLine() — Récupère la fin d'une ligne
 * @method ReflectionClass getExtension() — Récupère un objet ReflectionExtension pour l'extension définissant la classe
 * @method ReflectionClass getExtensionName() — Récupère le nom de l'extension qui définit la classe
 * @method ReflectionClass getFileName() — Récupère le nom du fichier déclarant la classe considérée
 * @method ReflectionClass getInterfaceNames() — Récupère les noms des interfaces
 * @method ReflectionClass getInterfaces() — Récupère les interfaces
 * @method ReflectionClass getMethod(string $name) — Récupère un objet ReflectionMethod pour une méthode d'une classe
 * @method ReflectionClass getMethods(null|int $filter = null) — Récupère un tableau de méthodes
 * @method ReflectionClass getModifiers() — Récupère les modificateurs de classe
 * @method ReflectionClass getName() — Récupère le nom de la classe
 * @method ReflectionClass getNamespaceName() — Récupère l'espace de noms
 * @method ReflectionClass getParentClass() — Récupère la classe parente
 * @method ReflectionClass getProperties(null|int $filter = null) — Récupère les propriétés
 * @method ReflectionClass getProperty(string $name) — Récupère une ReflectionProperty pour une propriété d'une classe
 * @method ReflectionClass getReflectionConstant(string $name) — Gets a ReflectionClassConstant for a class's constant
 * @method ReflectionClass getReflectionConstants() — Gets class constants
 * @method ReflectionClass getShortName() — Récupère le nom court d'une classe
 * @method ReflectionClass getStartLine() — Récupère le numéro de ligne de départ
 * @method ReflectionClass getStaticProperties() — Récupère les propriétés statiques
 * @method ReflectionClass getStaticPropertyValue(string $name , mixed &$def_value = null) — Récupère la valeur d'une propriété statique
 * @method ReflectionClass getTraitAliases() — Retourne un tableau des alias du trait
 * @method ReflectionClass getTraitNames() — Retourne un tableau de noms des traits utilisés par cette classe
 * @method ReflectionClass getTraits() — Retourne un tableau des traits utilisés par cette classe
 * @method ReflectionClass hasConstant(string $name) — Vérifie si une constante est définie
 * @method ReflectionClass hasMethod(string $name) — Vérifie si une méthode est définie
 * @method ReflectionClass hasProperty(string $name) — Vérifie si une propriété est définie
 * @method ReflectionClass implementsInterface(string $interface) — Vérifie si une classe implémente une interface
 * @method ReflectionClass inNamespace() — Vérifie si une classe est définie dans un espace de noms
 * @method ReflectionClass isAbstract() — Vérifie si une classe est abstraite
 * @method ReflectionClass isAnonymous() — Checks if class is anonymous
 * @method ReflectionClass isCloneable() — Renseigne à propos de la propriété de duplication de la classe
 * @method ReflectionClass isFinal() — Vérifie si une classe est finale
 * @method ReflectionClass isInstance(object $object) — Vérifie si une classe est une instance d'une autre classe
 * @method ReflectionClass isInstantiable() — Vérifie si une classe est instanciable
 * @method ReflectionClass isInterface() — Vérifie si une classe est une interface
 * @method ReflectionClass isInternal() — Vérifie si une classe est définie comme interne par une extension
 * @method ReflectionClass isIterable() — Check whether this class is iterable
 * @method ReflectionClass isIterateable() — Vérifie si la classe est itérable
 * @method ReflectionClass isSubclassOf(string $class) — Vérifie si la classe est une sous-classe
 * @method ReflectionClass isTrait() — Renseigne s'il s'agit d'un trait
 * @method ReflectionClass isUserDefined() — Vérifie si une classe a été définie dans l'espace utilisateur
 * @method ReflectionClass newInstance(mixed $args , ...$mixed) — Créer une nouvelle instance de la classe en utilisant les arguments fournis
 * @method ReflectionClass newInstanceArgs(array $args) — Créer une nouvelle instance en utilisant les arguments fournis
 * @method ReflectionClass newInstanceWithoutConstructor() — Crée une nouvelle instance de la classe sans invoquer le constructeur
 * @method ReflectionClass setStaticPropertyValue(string $name, string $value) — Définit la valeur d'une propriété statiques
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