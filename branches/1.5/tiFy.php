<?php

/**
 * @name tiFy
 * @namespace tiFy
 * @author Jordy Manner
 * @copyright Tigre Blanc Digital
 * @version 1.5.x
 */

namespace tiFy;

use Illuminate\Http\Request;
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Event\Emitter;
use Psr4ClassLoader;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\ServerBag;
use tiFy\Lib\File;

final class tiFy
{
    /**
     * Instance de la classe
     * @var self
     */
    protected static $instance;

    /**
     * Chemin absolu vers la racine de l'environnement.
     * @var resource
     */
    protected $absPath;

    /**
     * Chemin absolu vers la racine de presstiFy.
     * @var resource
     */
    protected $absDir;

    /**
     * Url absolue vers la racine la racine de presstiFy.
     * @var string
     */
    protected $absUrl;

    /**
     * Classe de rappel de la requête globale.
     * @var Request
     */
    protected $request;

    /**
     * Classe de rappel du conteneur d'injection de dépendance.
     * @var Container
     */
    protected $container;

    /**
     * Classe de rappel de gestion des événements.
     * @var Emitter
     */
    protected $emitter;

    /**
     * Attributs de configuration.
     * @var mixed
     */
    protected $config = [];

    /**
     * Classe de chargement automatique.
     * @var Psr4ClassLoader
     */
    protected $classLoader = null;

    /**
     * CONSTRUCTEUR
     *
     * @param string $absPath Chemin absolu vers la racine du projet
     *
     * @return void
     */
    public function __construct($absPath = null)
    {
        // Bypass
        if (defined('WP_INSTALLING') && (WP_INSTALLING === true)) :
            return;
        endif;

        if (self::$instance) :
            return;
        else :
            self::$instance = $this;
        endif;

        // Définition des chemins absolus
        $absPath = $absPath ? : (defined('PUBLIC_PATH') ? PUBLIC_PATH : ABSPATH);
        $this->absPath = rtrim(wp_normalize_path($absPath), '/') . '/';
        $this->absDir = dirname(__FILE__);

        // Définition des constantes d'environnement
        if (!defined('TIFY_CONFIG_DIR')) :
            define('TIFY_CONFIG_DIR', get_template_directory() . '/config');
        endif;
        if (!defined('TIFY_CONFIG_EXT')) :
            define('TIFY_CONFIG_EXT', 'yml');
        endif;

        /// Répertoire des stockage des plugins PresstiFy
        if (!defined('TIFY_PLUGINS_DIR')) :
            define('TIFY_PLUGINS_DIR', dirname(dirname($this->absDir())) . '/presstify-plugins');
        endif;

        // Instanciation du moteur
        $this->classLoad('tiFy', $this->absDir(). '/bin');

        // Instanciation des l'environnement des applicatifs
        $this->classLoad('tiFy\App', $this->absDir() . '/bin/app');

        // Instanciation des librairies proriétaires
        $this->getContainer()->share(Libraries::class, new Libraries());

        // Initialisation de la gestion des traductions
        $this->getContainer()->share(Languages::class, new Languages());

        // Définition de l'url absolue
        $this->absUrl = File::getFilenameUrl($this->absDir(), $this->absPath());

        // Instanciation des composants natifs
        $this->classLoad('tiFy\Core', __DIR__ . '/core');

        // Instanciation des extensions
        $this->classLoad('tiFy\Plugins', TIFY_PLUGINS_DIR);

        // Instanciation des jeux de fonctionnalités complémentaires
        $this->classLoad('tiFy\Set', $this->absDir() . '/set');

        // Instanciation des applicatifs
        $this->getContainer()->share(Apps::class, new Apps($this));
    }

    /**
     * Récupération de l'instance.
     *
     * @return \tiFy\tiFy
     *
     * @throws \LogicException
     */
    final public static function instance()
    {
        if (self::$instance instanceof static) :
            return self::$instance;
        endif;
    }

    /**
     *
     */
    final public function provide($alias, $args = [])
    {
        return $this->getContainer()->get($alias, $args);
    }

    /**
     * Récupération du chemin absolu vers la racine du projet Web.
     *
     * @return void
     */
    public function absPath()
    {
        return $this->absPath;
    }

    /**
     * Récupération du chemin absolu vers la racine de PresstiFy.
     *
     * @return void
     */
    public function absDir()
    {
        return $this->absDir;
    }

    /**
     * Récupération de l'url absolue vers la racine de PresstiFy.
     *
     * @return void
     */
    public function absUrl()
    {
        return $this->absUrl;
    }

    /**
     * Formatage lower_name d'une chaine de caratère
     * Converti une chaine de caractère CamelCase en snake_case
     * ex : _tiFyTest1_Test2 > _tiFy-test1_test2
     *
     * @param string $name
     * @param string $separator Caractère de séparation d'occurences
     *
     * @return string
     */
    public function formatLowerName($name, $separator = '-')
    {
        $parts = [];
        if (preg_match('#^_?tiFy#', $name, $match)) :
            $parts[] = reset($match);
            $name = preg_replace('#^_?tiFy#', '', $name);
        endif;
        $parts += array_map('lcfirst', preg_split('#(?=[A-Z])#', $name));

        if ($parts) :
            $name = '';
            foreach ($parts as $k => $part) :
                if (empty($part)) :
                    continue;
                elseif (!$k && preg_match('#^_?tiFy#', $part)) :
                    $name = $part;
                else :
                    $name .= preg_match('#_$#', $part) ? $part : "{$part}{$separator}";
                endif;
            endforeach;
            $name = rtrim($name, $separator);
        endif;

        return $name;
    }

    /**
     * Formatage UpperName d'une chaine de caratère
     * Converti une chaine de caractère snake_case en CamelCase
     * ex : _tiFy-test1_test2 > _tiFyTest1_Test2
     *
     * @param string $name Chaîne de caractère à traiter
     * @param bool $underscore Conservation des underscore
     *
     * @return string
     */
    public function formatUpperName($name, $underscore = true)
    {
        $name = join(($underscore ? '_' : ''), array_map('ucfirst', preg_split('#_#', $name)));
        $name = join('', array_map('ucfirst', preg_split('#-#', $name)));
        $name = preg_replace('#^(_)?(T)(iFy)#', '$1t$3', $name);

        return $name;
    }

    /**
     * Chargement automatique des classes.
     *
     * @param string $namespace Espace de nom
     * @param string|NULL $base_dir Chemin vers le repertoire
     * @param string|NULL $bootstrap Nom de la classe à instancier
     *
     * @return void
     */
    public function classLoad($namespace, $base_dir = null, $bootstrap = null)
    {
        if (is_null($this->classLoader)) :
            require_once __DIR__ . '/bin/lib/ClassLoader/Psr4ClassLoader.php';
            $this->classLoader = new \Psr4ClassLoader;
        endif;

        if (!$base_dir) :
            $base_dir = dirname(__FILE__);
        endif;

        $this->classLoader->addNamespace($namespace, $base_dir, false);
        $this->classLoader->register();

        if ($bootstrap) :
            $classname = "\\" . ltrim($namespace, '\\') . "\\" . $bootstrap;

            if (class_exists($classname)) :
                new $classname;
            endif;
        endif;
    }

    /**
     * Récupération de la classe de rappel de la requête global
     *
     * @return Request
     */
    public function getRequest()
    {
        if (! $this->request) :
            $this->request = Request::createFromGlobals();
        endif;

        return $this->request;
    }

    /**
     * Récupération de la classe de rappel de propriété de la requête globale.
     *
     * @see https://laravel.com/api/5.6/Illuminate/Http/Request.html
     * @see https://symfony.com/doc/current/components/http_foundation.html
     * @see http://api.symfony.com/4.0/Symfony/Component/HttpFoundation/ParameterBag.html
     *
     * @param string $property Propriété de la requête à traiter $_POST (alias post, request)|$_GET (alias get, query)|$_COOKIE (alias cookie, cookies)|attributes|$_FILES (alias files)|SERVER (alias server)|headers.
     *
     * @return Request|FileBag|HeaderBag|ParameterBag|ServerBag
     */
    public function request($property = '')
    {
        if (! $request = $this->getRequest()) :
            return null;
        endif;

        switch (strtolower($property)) :
            default :
                return $request;
                break;
            case 'post' :
            case 'request' :
                return $request->request;
                break;
            case 'get' :
            case 'query' :
                return $request->query;
                break;
            case 'cookie' :
            case 'cookies' :
                return $request->cookies;
                break;
            case 'attributes' :
                return $request->attributes;
                break;
            case 'files' :
                return $request->files;
                break;
            case 'server' :
                return $request->server;
                break;
            case 'headers' :
                return $request->headers;
                break;
        endswitch;
    }

    /**
     * Appel d'une méthode de la classe de rappel de la requête global.
     *
     * @see https://laravel.com/api/5.6/Illuminate/Http/Request.html
     * @see https://symfony.com/doc/current/components/http_foundation.html
     * @see http://api.symfony.com/4.0/Symfony/Component/HttpFoundation/ParameterBag.html
     *
     * @param string $method Nom de la méthode à appeler (all|keys|replace|add|get|set|has|remove|getAlpha|getAlnum|getBoolean|getDigits|getInt|filter)
     * @param array $args Tableau associatif des arguments passés dans la méthode.
     * @param string $property Propriété de la requête à traiter $_POST (alias post, request)|$_GET (alias get, query)|$_COOKIE (alias cookie, cookies)|attributes|$_FILES (alias files)|SERVER (alias server)|headers.
     *
     * @return mixed
     */
    public function requestCall($method, $args = [], $property = '')
    {
        if (!$request = $this->getRequest()) :
            return null;
        endif;

        $object = $this->request($property);

        if (method_exists($object, $method)) :
            return call_user_func_array([$object, $method], $args);
        endif;

        return null;
    }

    /**
     * Récupération de la classe de rappel du conteneur d'injection de dépendances
     * @see http://container.thephpleague.com/
     *
     * @return Container
     */
    public function getContainer()
    {
        if (! $this->container) :
            $this->container = new Container();
            /*$this->container->delegate(
                new ReflectionContainer()
            );*/
        endif;

        return $this->container;
    }

    /**
     * Récupération de la classe de rappel de gestion des événements
     * @see http://event.thephpleague.com/2.0/
     *
     * @return Emitter
     */
    public function getEmitter()
    {
        if (! $this->emitter) :
            $this->emitter = new Emitter();
        endif;

        return $this->emitter;
    }

    /**
     * Récupération d'attributs de configuration globale
     *
     * @param NULL|string $attr Attribut de configuration
     * @param string $default Valeur de retour par défaut
     *
     * @return mixed|$default
     */
    public function getConfig($attr = null, $default = '')
    {
        if (is_null($attr)) :
            return $this->config;
        endif;

        if (isset($this->config[$attr])) :
            return $this->config[$attr];
        endif;

        return $default;
    }

    /**
     * Définition d'un attribut de configuration globale
     *
     *
     */
    public function setConfig($key, $value = '')
    {
        $this->config[$key] = $value;
    }
}
