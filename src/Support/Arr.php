<?php declare(strict_types=1);

namespace tiFy\Support;

use Illuminate\Support\Arr as IlluminateArr;

/**
 * Class Str
 * @package tiFy\Support
 *
 * @mixin IlluminateArr
 */
class Arr
{
    /**
     * Appel statique de l'héritage des méthodes de la classe Str de Laravel.
     * {@internal Utile pour l'appel statique interne à Illuminate\Support\Str}
     *
     * @param string $name Nom de qualification de la méthode.
     * @param array $args Liste des variables passées en argument à la méthode.
     *
     * @return mixed
     */
    public static function __callstatic($name, $args)
    {
        if (method_exists(IlluminateArr::class, $name)) :
            return call_user_func_array([IlluminateArr::class, $name], $args);
        endif;

        return null;
    }

    /**
     * Appel de l'héritage des méthodes statiques de la classe Str de Laravel.
     *
     * @param string $name Nom de qualification de la méthode.
     * @param array $args Liste des variables passées en argument à la méthode.
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (method_exists(IlluminateArr::class, $name)) :
            return call_user_func_array([IlluminateArr::class, $name], $args);
        endif;

        return null;
    }
}