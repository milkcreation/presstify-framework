<?php
namespace PresstiFy\Set\Theme;

class Theme extends \tiFy\App\Factory
{
    /* = ARGUMENTS = */
    // Liste des Actions à déclencher
    protected $tFyAppActions                = array(
        'init',
        'theme_enqueue_scripts'
    );

    // Fonctions de rappel des actions
    protected $tFyAppActionsMethods    = array(
        'init'                              => 'register_scripts',
        'theme_before_enqueue_scripts'      => 'enqueue_scripts'
    );
    
    /* = FILTRES = */
    /** == Déclaration des scripts == **/
    public function register_scripts()
    {
        $min = SCRIPT_DEBUG ? '' : '.min';
        $version = 170319;

        // STYLES                
        /// THEME - Bootswatch
        tify_register_style(
            'presstiFyBootswatch',
            array(
                'src'        => 'https://bootswatch.com/lumen/bootstrap.min.css',
                'version'    => '3.3.7',
                'deps'       => array(
                    'bootstrap'
                )  
            )
        );
        
        /// THEME - Bootswatch
        tify_register_style(
            'presstiFyTheme',
            array(
                'src'        => get_stylesheet_directory_uri(). '/vendor/presstify.com/collections/Theme/css/theme.css',
                'version'    => $version,
                'deps'       => array(
                    'themeRoot'
                )  
            )
        );
    }

    /** == Mise en file des scripts de l'interface utilisateur == **/
    public function enqueue_scripts()
    {
        wp_enqueue_style( 'presstiFyBootswatch' );
        wp_enqueue_style( 'presstiFyTheme' );
    }
}