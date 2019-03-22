<?php declare(strict_types=1);

namespace tiFy;

use tiFy\Kernel\Composer\ClassLoader;
use tiFy\Kernel\Container\Container;
use tiFy\Kernel\Config\Config;
use tiFy\Kernel\Filesystem\Paths;
use tiFy\Kernel\KernelServiceProvider;

/**
 * Class tiFy
 *
 * @desc PresstiFy -- Framework Milkcreation.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy
 * @version 2.0.95
 * @copyright Milkcreation
 */
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
        /** Ultra-prioritaire */
        Paths::class,
        ClassLoader::class,
        Config::class,
        /** ----------------- */
        KernelServiceProvider::class
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        if (defined('WP_INSTALLING') && (WP_INSTALLING === true)) :
            return;
        endif;

        if (!self::$instance) :
            self::$instance = $this;
        else :
            return;
        endif;

        add_action('plugins_loaded', function() {
            load_muplugin_textdomain('tify', '/presstify/languages/');
            do_action('tify_load_textdomain');
        });

        add_action('after_setup_tify', function () {
            do_action('tify_app_boot');
        }, 0);

        parent::__construct();
    }

    /**
     * Récupération de l'instance courante.
     *
     * @return null|static
     */
    final public static function instance(): ?tiFy
    {
        if (self::$instance instanceof static) :
            return self::$instance;
        endif;

        return null;
    }

    function hasParameter()
    {
        return false;
    }
}
