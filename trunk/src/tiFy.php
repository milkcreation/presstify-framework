<?php

/**
 * @name tiFy
 * @namespace tiFy
 * @author Jordy Manner
 * @copyright Milkcreation
 * @version 2.0.0
 */

namespace tiFy;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Container\ServiceProvider\ServiceProviderInterface;
use League\Event\Emitter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\ServerBag;
use tiFy\Apps\AppsServiceProvider;

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
     * Classe de chargement automatique.
     * @var Psr4ClassLoader
     */
    public $classLoader = null;

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
     * Classe de rappel de la requête globale.
     * @var Request
     */
    protected $globalRequest;

    /**
     * Attributs de configuration.
     * @var mixed
     */
    protected $config = [];

    /**
     * CONSTRUCTEUR.
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

        if (! self::$instance) :
            $this->setInstance();
        else :
            return;
        endif;

        // Définition des chemins
        $absPath = $absPath ? : (defined('PUBLIC_PATH') ? PUBLIC_PATH : ABSPATH);
        $this->absPath = rtrim(wp_normalize_path($absPath), '/') . '/';
        $this->absDir = dirname(__FILE__);
        $rel = (new fileSystem())->makePathRelative($this->absDir(), $this->absPath());
        $this->absUrl = home_url($rel);

        // Définition des constantes d'environnement
        // Répertoire stockage de la configuration
        if (!defined('TIFY_CONFIG_DIR')) :
            define('TIFY_CONFIG_DIR', get_template_directory() . '/config');
        endif;

        // Répertoire de stockage des plugins PresstiFy
        if (!defined('TIFY_PLUGINS_DIR')) :
            define('TIFY_PLUGINS_DIR', dirname(dirname($this->absDir())) . '/presstify-plugins');
        endif;

        add_action('after_setup_theme', [$this, 'after_setup_theme'], 0);
    }

    /**
     * A l'issue de l'initialisation du thème.
     *
     * @return void
     */
    final public function after_setup_theme()
    {
        // Traitement de la configuration
        $finder = (new Finder())->files()->name('/\.php$/')->in(TIFY_CONFIG_DIR);
        foreach($finder as $file) :
            $keys = preg_split('#' . DIRECTORY_SEPARATOR . '#', $file->getRelativePath(), NULL, PREG_SPLIT_NO_EMPTY);
            $keys[] = basename($file->getFilename(), ".{$file->getExtension()}");

            $this->setConfig(implode('.', $keys), include($file->getRealPath()));
        endforeach;

        // Chargement automatique des classes
        foreach($this->getConfig('autoload', []) as $type => $value) :
            foreach($value as $namespace => $path) :
                $this->classLoad($namespace, $path, $type);
            endforeach;
        endforeach;
        $this->classLoad("tiFy\\Plugins\\", TIFY_PLUGINS_DIR);

        // Déclenchement de la méthode de démarrage des applications
        add_action('after_setup_tify', function() { do_action('tify_app_boot'); }, 9999);

        // Initialisation du fournisseur de service des applications
        $apps = new AppsServiceProvider($this);
        $this->serviceShare(AppsServiceProvider::class, $apps);
        $this->serviceProvider($apps);

        // Chargement des traductions
        do_action('tify_load_textdomain');
    }

    /**
     * Récupération de l'instance courante.
     *
     * @return $this
     */
    final public static function instance()
    {
        if (self::$instance instanceof static) :
            return self::$instance;
        endif;
    }

    /**
     * Définition de l'instance courante.
     *
     * @return $this
     */
    private function setInstance()
    {
        if (! self::$instance) :
            self::$instance = $this;
        endif;

        $this->serviceShare(__CLASS__, $this);
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
     * Récupération de la classe de traitement des applications.
     *
     * @return AppsServiceProvider
     */
    public function apps()
    {
        return $this->serviceGet(AppsServiceProvider::class);
    }

    /**
     * Récupération d'attribut de configuration.
     *
     * @param null|string $key Clé d'index de l'attribut de configuration. Renvoie la liste complète si null.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getConfig($key = null, $default = '')
    {
        if (is_null($key)) :
            return $this->config;
        endif;

        return Arr::get($this->config, $key, $default);
    }

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'index de l'attribut de configuration.
     * @param mixed $valeur Valeur de l'attribut de configuration.
     *
     * @return void
     */
    public function setConfig($key, $value)
    {
        Arr::set($this->config, $key, $value);
    }

    /**
     * Chargement automatique des classes.
     *
     * @param string $prefix Espace de nom de qualification.
     * @param array|string $base_dir Chemin vers le repertoire de la classe.
     *
     * @return void
     */
    public function classLoad($prefix, $paths, $type = 'psr-4')
    {
        if (! $this->classLoader) :
            $this->classLoader = new ClassLoader();
        endif;

        switch($type) :
            default :
            case 'psr-4' :
                $this->classLoader->addPsr4($prefix, $paths);
                break;
            case 'psr-0' :
                $this->classLoader->add($prefix, $paths);
                break;
            case 'classmap' :
                /** @todo */
                break;
            case 'files' :
                /** @todo */
                break;
        endswitch;

        $this->classLoader->register();
    }

    /**
     * Récupération de la classe de rappel du conteneur d'injection de dépendances.
     * @see http://container.thephpleague.com/
     *
     * @return Container
     */
    public function getContainer()
    {
        if (! $this->container) :
            $this->container = new Container();
            /**
             * Activation de l'auto-wiring
             * @see http://container.thephpleague.com/2.x/auto-wiring/
             */
            //$this->container->delegate(new ReflectionContainer());
        endif;

        return $this->container;
    }

    /**
     * Déclaration d'un service (singleton|multiton).
     * @see http://container.thephpleague.com/2.x/getting-started/
     *
     * @param string $alias Identificant de quailifcation du service
     * @param null|string|Closure|object $concrete Définition du service.
     * @param bool $share Activation/Désactivation d'instance multiples. Si true, instance unique > singleton.
     *
     * @return object
     */
    public function serviceAdd($alias, $concrete = null, $share = false)
    {
        return $this->getContainer()->add($alias, $concrete, $share);
    }

    /**
     * Déclaration d'une instance unique de service (singleton).
     * @see http://container.thephpleague.com/2.x/getting-started/
     *
     * @param string $alias Identificant de quailifcation du service
     * @param null|string|Closure|object $concrete Définition du service.
     *
     * @return object
     */
    public function serviceShare($alias, $concrete = null)
    {
        return $this->serviceAdd($alias, $concrete, true);
    }

    /**
     * Vérification d'existance d'un service déclaré.
     *
     * @param string $alias Identificant de quailifcation du service
     * @param array $args Liste des variables passée en argument.
     *
     * @return object
     */
    public function serviceHas($alias)
    {
        return $this->getContainer()->has($alias);
    }

    /**
     * Récupération d'un service déclaré.
     *
     * @param string $alias Identificant de quailifcation du service
     * @param array $args Liste des variables passée en argument.
     *
     * @return object
     */
    public function serviceGet($alias, $args = [])
    {
        return $this->getContainer()->get($alias, $args);
    }

    /**
     * Déclaration d'un fournisseur de d'un services.
     *
     * @param ServiceProviderInterface $provider Classe de rappel du fournisseur de service.
     *
     * @return object
     */
    public function serviceProvider(ServiceProviderInterface $provider)
    {
        return $this->getContainer()->addServiceProvider($provider);
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
        if (! $this->globalRequest) :
            $this->globalRequest = Request::createFromGlobals();
        endif;

        switch (strtolower($property)) :
            default :
                return $this->globalRequest;
                break;
            case 'post' :
            case 'request' :
                return $this->globalRequest->request;
                break;
            case 'get' :
            case 'query' :
                return $this->globalRequest->query;
                break;
            case 'cookie' :
            case 'cookies' :
                return $this->globalRequest->cookies;
                break;
            case 'attributes' :
                return $this->globalRequest->attributes;
                break;
            case 'files' :
                return $this->globalRequest->files;
                break;
            case 'server' :
                return $this->globalRequest->server;
                break;
            case 'headers' :
                return $this->globalRequest->headers;
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
        $object = $this->request($property);

        if (method_exists($object, $method)) :
            return call_user_func_array([$object, $method], $args);
        endif;

        return null;
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
}
