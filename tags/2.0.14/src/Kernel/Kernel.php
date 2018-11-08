<?php

namespace tiFy\Kernel;

use tiFy\App\Container\AppContainer;
use tiFy\Contracts\App\AppInterface;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Kernel\ClassInfo\ClassInfo;
use tiFy\Kernel\Composer\ClassLoader;
use tiFy\Kernel\Config\Config;
use tiFy\Kernel\Container\Container;
use tiFy\Kernel\Events\Manager as Events;
use tiFy\Kernel\Filesystem\Paths;
use tiFy\Kernel\Http\Request;
use tiFy\tiFy;

/**
 * @method static AppInterface|AppContainer App()
 * @method static ClassInfo ClassInfo(string|object $class)
 * @method static ClassLoader ClassLoader()
 * @method static Container Container()
 * @method static Config Config()
 * @method static Events Events()
 * @method static Logger Logger()
 * @method static Paths Paths()
 * @method static Request Request()
 * @method static ViewEngine ViewEngine()
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
            case 'Events' :
                $alias = 'events';
                break;
            case 'Paths' :
                $alias = Paths::class;
                break;
            case 'Request' :
                $alias = 'request';
                break;
            case 'ViewEngine' :
                $alias = 'view.engine';
                break;
        endswitch;

        return tiFy::instance()->resolve($alias, $args);
    }
}