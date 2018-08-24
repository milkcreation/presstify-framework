<?php

namespace tiFy\Deprecated;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Event\Emitter;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\ServerBag;

trait DeprecatedtiFyTrait
{
    /**
     * Chemin absolu vers la racine de presstiFy
     * @var string
     */
    public static $AbsDir;

    /**
     * Chemin absolu vers la racine de l'environnement.
     * @var string
     */
    public static $AbsPath;

    /**
     * Url absolue vers la racine la racine de presstiFy
     * @var string
     */
    public static $AbsUrl;

    /**
     * Classe de chargement automatique
     */
    public static $ClassLoader = null;

    /**
     * Attributs de configuration
     * @var mixed
     */
    public static $Config = [];

    /**
     * Classe de rappel du conteneur d'injection de dépendance
     * @var Container
     */
    public static $Container;

    /**
     * Classe de rappel de gestion des événements
     * @var Emitter
     */
    public static $Emitter;

    /**
     * Classe de rappel de la requête globale
     * @var Request
     */
    public static $GlobalRequest;

    /**
     * Chargement automatique des classes
     *
     * @param string $namespace Espace de nom
     * @param string|NULL $base_dir Chemin vers le repertoire
     * @param string|NULL $bootstrap Nom de la classe à instancier
     *
     * @return void
     */
    public static function classLoad($namespace, $base_dir = null, $bootstrap = null)
    {
        if (is_null(self::$ClassLoader)) :
            require_once __DIR__ . '/../lib/ClassLoader/Psr4ClassLoader.php';
            self::$ClassLoader = new \Psr4ClassLoader;
        endif;

        if (!$base_dir) :
            $base_dir = dirname(__FILE__);
        endif;

        self::$ClassLoader->addNamespace($namespace, $base_dir, false);
        self::$ClassLoader->register();

        if ($bootstrap) :
            $classname = "\\" . ltrim($namespace, '\\') . "\\" . $bootstrap;

            if (class_exists($classname)) :
                new $classname;
            endif;
        endif;
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
    public static function formatLowerName($name, $separator = '-')
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
    public static function formatUpperName($name, $underscore = true)
    {
        $name = join(($underscore ? '_' : ''), array_map('ucfirst', preg_split('#_#', $name)));
        $name = join('', array_map('ucfirst', preg_split('#-#', $name)));
        $name = preg_replace('#^(_)?(T)(iFy)#', '$1t$3', $name);

        return $name;
    }

    /**
     * Récupération d'attributs de configuration globale
     *
     * @param NULL|string $attr Attribut de configuration
     * @param string $default Valeur de retour par défaut
     *
     * @return mixed|$default
     */
    public static function getConfig($attr = null, $default = '')
    {
        if (is_null($attr)) :
            return self::$Config;
        endif;

        return Arr::get(self::$Config, $attr, $default);
    }

    /**
     * Récupération de la classe de rappel du conteneur d'injection de dépendances
     * @see http://container.thephpleague.com/
     *
     * @return self
     */
    public static function getContainer()
    {
        return self::instance();
    }

    /**
     * Récupération de la classe de rappel de gestion des événements
     * @see http://event.thephpleague.com/2.0/
     *
     * @return Emitter
     */
    public static function getEmitter()
    {
        if (! self::$Emitter) :
            self::$Emitter = new Emitter();
        endif;

        return self::$Emitter;
    }

    /**
     * Récupération de la classe de rappel de la requête global
     *
     * @return Request
     */
    public static function getGlobalRequest()
    {
        if (!self::$GlobalRequest) :
            self::$GlobalRequest = Request::createFromGlobals();
        endif;

        return self::$GlobalRequest;
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
    public static function request($property = '')
    {
        if (!$request = self::getGlobalRequest()) :
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
    public static function requestCall($method, $args = [], $property = '')
    {
        if (!$request = self::getGlobalRequest()) :
            return null;
        endif;

        $object = self::request($property);

        if (method_exists($object, $method)) :
            return call_user_func_array([$object, $method], $args);
        endif;

        return null;
    }

    /**
     * Définition d'un attribut de configuration globale
     *
     * @return void
     */
    public static function setConfig($key, $value = '')
    {
        return Arr::set(self::$Config, $key, $value);
    }
}