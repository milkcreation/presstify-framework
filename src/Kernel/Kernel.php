<?php

namespace tiFy\Kernel;

use tiFy\App\AppInterface;
use tiFy\Kernel\Assets\Assets;
use tiFy\Kernel\ClassInfo\ClassInfo;
use tiFy\Kernel\Composer\ClassLoader;
use tiFy\Kernel\Config\Config;
use tiFy\Kernel\Container\Container;
use tiFy\Kernel\Filesystem\Paths;
use tiFy\Kernel\Http\Request;
use tiFy\Kernel\Templates\EngineInterface;
use tiFy\tiFy;

/**
 * @method static AppInterface App()
 * @method static Assets Assets()
 * @method static ClassInfo ClassInfo(string|object $class)
 * @method static ClassLoader ClassLoader()
 * @method static Container Container()
 * @method static Config Config()
 * @method static Events Events()
 * @method static Logger Logger()
 * @method static Paths Paths()
 * @method static Request Request()
 * @method static EngineInterface TemplatesEngine()
 */
class Kernel
{
    /**
     * Instance de la classe
     * @return self
     */
    protected static $instance;

    /**
     * Appel static d'une librairies de la boîte à outils
     *
     * @param $name
     * @param $arguments
     *
     * @return callable
     */
    public static function __callStatic($name, $args)
    {
        if(!self::$instance instanceof self) :
            $self = self::$instance = new static();
        else :
            $self = self::$instance;
        endif;

        switch($name) :
            default :
                $alias = "tiFy\\Kernel\\{$name}\\{$name}";
                break;
            case 'App':
                return tiFy::instance()->resolve(\App\App::class);
                break;
            case 'Container' :
                return tiFy::instance();
                break;
            case 'Paths' :
                $alias = Paths::class;
                break;
            case 'Request' :
                $alias = 'tiFyRequest';
                break;
            case 'TemplatesEngine' :
                $alias = EngineInterface::class;
                break;
        endswitch;

        return tiFy::instance()->resolve($alias, $args);
    }
}