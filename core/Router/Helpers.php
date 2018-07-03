<?php

use tiFy\Core\Router\Router;

if (!function_exists('tify_router_content_hook_is')) :

    /**
     * Vérification d'existance d'une page associée à l'identifiant de qualification de la route.
     *
     * @param string $name Identifiant de qualification de la route.
     * @param null|int|\WP_Post| $post Post Wordpress courant|Identifiant de qualification du post|Object Post Wordpress.
     *
     * @return bool
     */
    function tify_router_content_hook_is($name, $post = null)
    {
        return Router::get()->isContentHook($name, $post);
    }
endif;

if (!function_exists('tify_router_content_hook_get')) :

    /**
     * Récupération de la page associée à l'identifiant de qualification de la route.
     *
     * @param string $name Identifiant de qualification de la route.
     * @param int $default Valeur de retour par défaut.
     *
     * @return int
     */
    function tify_router_content_hook_get($name, $default = 0)
    {
        return Router::get()->getContentHook($name, $default);
    }
endif;

if (!function_exists('tify_router_content_hook_permalink')) :

    /**
     * Récupération de la page associée à l'identifiant de qualification de la route.
     *
     * @param string $name Identifiant de qualification de la route.
     *
     * @return int
     */
    function tify_router_content_hook_permalink($name)
    {
        return Router::get()->getContentHookPermalink($name);
    }
endif;