<?php

use tiFy\Kernel\Kernel;

/**
 * KERNEL
 * ---------------------------------------------------------------------------------------------------------------------
 */
if (!function_exists('assets')) :
    /**
     * Assets - Controleur des assets.
     * @see \tiFy\Kernel\Assets\Assets
     *
     * @return string
     */
    function assets()
    {
        return Kernel::Assets();
    }
endif;

if (!function_exists('class_info')) :
    /**
     * ClassInfo - Controleur d'informations sur une classe.
     * @see \tiFy\Kernel\ClassInfo\ClassInfo
     *
     * @param string|object Nom complet ou instance de la classe.
     *
     * @return string
     */
    function class_info($class)
    {
        return Kernel::ClassInfo($class);
    }
endif;

if (!function_exists('class_loader')) :
    /**
     * ClassLoader - Controleur de déclaration d'espaces de nom et d'inclusion de fichier automatique.
     *
     * @return \tiFy\Kernel\Composer\ClassLoader
     */
    function class_loader()
    {
        return Kernel::ClassLoader();
    }
endif;

if (!function_exists('config')) :
    /**
     * Config - Controleur de configuration.
     *
     * @return \tiFy\Kernel\Config\Config
     */
    function config()
    {
        return Kernel::Config();
    }
endif;

if (!function_exists('container')) :
    /**
     * Container - Controleur d'injection de dépendances.
     *
     * @return \tiFy\Kernel\Container\Container
     */
    function container()
    {
        return Kernel::Container();
    }
endif;

if (!function_exists('events')) :
    /**
     * Events - Controleur d'événements.
     *
     * @return \tiFy\Kernel\Events\Events
     */
    function events()
    {
        return Kernel::Events();
    }
endif;

if (!function_exists('logger')) :
    /**
     * Logger - Controleur de journalisation des actions.
     *
     * @return \tiFy\Kernel\Logger\Logger
     */
    function logger()
    {
        return Kernel::Logger();
    }
endif;

if (!function_exists('paths')) :
    /**
     * Paths - Controleur des chemins vers les repertoires de l'application.
     *
     * @return \tiFy\Kernel\Filesystem\Paths
     */
    function paths()
    {
        return Kernel::Paths();
    }
endif;

if (!function_exists('request')) :
    /**
     * Request
     *
     * @return \tiFy\Kernel\Http\Request
     */
    function request()
    {
        return Kernel::Request();
    }
endif;