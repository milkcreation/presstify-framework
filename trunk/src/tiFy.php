<?php

/**
 * @name tiFy
 * @namespace tiFy
 * @author Jordy Manner
 * @copyright Milkcreation
 * @version 2.0.0
 */

namespace tiFy;

use tiFy\Kernel\Container\Container;
use tiFy\Kernel\KernelServiceProvider;

final class tiFy extends Container
{
    /**
     * Instance de la classe
     * @var self
     */
    protected static $instance;

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        KernelServiceProvider::class
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        // Bypass
        if (defined('WP_INSTALLING') && (WP_INSTALLING === true)) :
            return;
        endif;

        if (!self::$instance) :
            self::$instance = $this;
        else :
            return;
        endif;

        add_action(
            'after_setup_tify',
            function () {
                do_action('tify_app_boot');
            },
            9999
        );

        parent::__construct();

        add_action('plugins_loaded', [$this, 'plugins_loaded']);
    }

    /**
     * Après le chargement des plugins.
     *
     * @return void
     */
    public function plugins_loaded()
    {
        load_muplugin_textdomain('tify', '/presstify/languages/');

        do_action('tify_load_textdomain');
    }

    /**
     * Récupération de l'instance courante.
     *
     * @return $this
     */
    final public static function instance()
    {
        if (self::$instance instanceof static) :
            return self::$instance;
        endif;
    }
}