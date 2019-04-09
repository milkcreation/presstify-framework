<?php

namespace tiFy\Components\Tools\Functions;

/**
 * Class Functions
 * @package tiFy\Components\Tools\Functions
 *
 * @deprecated Utiliser tiFy\Support\Callback en remplacement
 */
class Functions
{
    /**
     * Vérifie si une variable peut être appelée en tant que fonction.
     *
     * @return bool
     */
    public function isCallable($var)
    {
        return is_string($var)
            ? (preg_match('#\\\#', $var) && is_callable($var, true))
            : is_callable($var, true);
    }
}