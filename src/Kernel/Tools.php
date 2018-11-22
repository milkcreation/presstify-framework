<?php

namespace tiFy\Kernel;

use tiFy\Components\Tools\Checker\Checker;
use tiFy\Components\Tools\Cryptor\Cryptor;
use tiFy\Components\Tools\File\File;
use tiFy\Components\Tools\Functions\Functions;
use tiFy\Components\Tools\Html\Html;
use tiFy\Kernel\Notices\Notices;
use tiFy\Components\Tools\Str\Str;
use tiFy\Components\Tools\User\User;
use tiFy\tiFy;

/**
 * @method static Checker Checker()
 * @method static Cryptor Cryptor()
 * @method static File File()
 * @method static Functions Functions()
 * @method static Html Html()
 * @method static Notices Notices()
 * @method static Str Str()
 * @method static User User()
 */
class Tools
{
    /**
     * Appel statique d'une librairie de la boîte à outils.
     *
     * @param string $name Nom de qualification de la librairie.
     * @param array $args Liste des variables passées en argument à la méthode.
     *
     * @return callable
     */
    public static function __callStatic($name, $args)
    {
        $alias = "tiFy\\Components\\Tools\\{$name}\\{$name}";
        if (!tiFy::instance()->has($alias)) :
            if (!class_exists($alias)) :
                wp_die(sprintf(__('La boîte à outils "%s" ne semble pas disponible', 'tify'), $name), __('Librairie indisponible', 'tify'), 500);
            endif;
            tiFy::instance()->add($alias);
        endif;

        return tiFy::instance()->get($alias, $args);
    }
}