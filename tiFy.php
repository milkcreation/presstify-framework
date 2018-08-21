<?php

/**
 * @name tiFy
 * @namespace tiFy
 * @author Jordy Manner
 * @copyright Tigre Blanc Digital
 * @version 1.4.62
 */

namespace tiFy;

use tiFy\Kernel\Container\Container;
use tiFy\Kernel\KernelServiceProvider;
use tiFy\Lib\File;
use tiFy\Deprecated\DeprecatedtiFyTrait as DeprecatedTrait;

final class tiFy extends Container
{
    use DeprecatedTrait;

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
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct($AbsPath = null)
    {
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

        // Définition des chemins absolus
        $AbsPath = $AbsPath ? : (defined('PUBLIC_PATH') ? PUBLIC_PATH : ABSPATH);
        self::$AbsPath = rtrim(wp_normalize_path($AbsPath), '/') . '/';
        self::$AbsDir = dirname(__FILE__);

        // Définition des constantes d'environnement
        if (!defined('TIFY_CONFIG_DIR')) :
            define('TIFY_CONFIG_DIR', get_stylesheet_directory() . '/config');
        endif;
        if (!defined('TIFY_CONFIG_EXT')) :
            define('TIFY_CONFIG_EXT', 'yml');
        endif;
        /// Répertoire des plugins
        if (!defined('TIFY_PLUGINS_DIR')) :
            define('TIFY_PLUGINS_DIR', dirname(dirname(self::$AbsDir)) . '/presstify-plugins');
        endif;

        // Instanciation des controleurs en maintenance
        self::classLoad('tiFy\Maintenance', self::$AbsDir . '/bin/maintenance', 'Maintenance');

        // Instanciation des librairies proriétaires
        new Libraries;

        // Initialisation de la gestion des traductions
        new Languages;

        // Instanciation des fonctions d'aides au développement
        self::classLoad('tiFy\Helpers', __DIR__ . '/helpers');

        // Définition de l'url absolue
        self::$AbsUrl = File::getFilenameUrl(self::$AbsDir, self::$AbsPath);

        // Instanciation des fonctions d'aide au développement
        new Helpers;

        require_once __DIR__ . '/Helpers.php';

        parent::__construct();

        // Instanciation des applicatifs
        new Apps;
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
