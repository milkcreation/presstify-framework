<?php

use tiFy\Wordpress\Contracts\Wordpress;

if (!function_exists('wordpress')) {
    /**
     * Instance du gestionnaire d'environnement Wordpress.
     *
     * @return Wordpress
     */
    function wordpress(): Wordpress
    {
        return app()->get('wp');
    }
}