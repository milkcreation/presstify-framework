<?php

namespace tiFy\Kernel;

use tiFy\Apps\AppTrait;
use tiFy\Components\Tools\Checker\Checker;
use tiFy\Components\Tools\Cryptor\Cryptor;
use tiFy\Components\Tools\Html\Html;
use tiFy\Components\Tools\File\File;
use tiFy\Components\Tools\User\User;

/**
 * @method static Checker Checker()
 * @method static Cryptor Cryptor()
 * @method static Html Html()
 * @method static File File()
 * @method static User User()
 */
class Tools
{
    use AppTrait;

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

        $alias = "tiFy\\Components\\Tools\\{$name}\\{$name}";
        if (!$self->appServiceHas($alias)) :
            if (!class_exists($alias)) :
                wp_die(sprintf(__('La boîte à outils "%s" ne semble pas disponible', 'tify'), $name), __('Librairie indisponible', 'tify'), 500);
            endif;
            $self->appServiceAdd($alias);
        endif;

        return $self->appServiceGet($alias, $args);
    }
}