<?php
namespace App;

class ScriptLoader extends \tiFy\App\Factory
{
    /**
     * Liste des Actions à déclencher
     */
    protected $tFyAppActions                = array(
        'init',
        'wp_enqueue_scripts',
        'admin_enqueue_scripts',
        'login_enqueue_scripts'
    );

    /**
     * Fonctions de rappel des actions
     */
    protected $tFyAppActionsMethods    = array(
        'init'                  => 'register_scripts',
        'wp_enqueue_scripts'    => 'enqueue_scripts'
    );

    /**
     * DECLENCHEURS
     */
    /**
     * Déclaration des scripts
     */
    public function register_scripts()
    {
        $min = SCRIPT_DEBUG ? '' : '.min';
        $version = 170319;
        
        // STYLES                        
        /// POLICE DE CARACTERE GOOGLE DU THEME
        tify_register_style(
            'themeFontGoogle',
            array(
                'src'        => '//fonts.googleapis.com/css?family=Libre+Franklin:100,400,700,900',
                'version'    => $version
            )
        );
        
        /// THEME - Reset (privé ne pas modifier)
        tify_register_style(
            'themeReset',
            array(
                'src'        => get_stylesheet_directory_uri(). '/css/_reset'. $min .'.css',
                'version'    => $version
            )
        );
        
        /// THEME - Helpers (privé ne pas modifier)
        tify_register_style(
            'themeHelpers',
            array(
                'src'        => get_stylesheet_directory_uri(). '/css/_helpers'. $min .'.css',
                'version'    => $version
            )
        );
        
        /// THEME - Couleurs
        tify_register_style(
            'themeColors',
            array(
                'src'        => get_stylesheet_directory_uri(). '/css/colors'. $min .'.css',
                'version'    => $version
            )
        );
        
        /// THEME - Elements graphique (bouton, étiquettes ...)
        tify_register_style(
            'themeElements',
            array(
                'src'        => get_stylesheet_directory_uri(). '/css/elements'. $min .'.css',
                'version'    => $version
            )
        );
        
        /// THEME - Modèles de composition (totem, diaporama, ... )
        tify_register_style(
            'themeModels',
            array(
                'src'        => get_stylesheet_directory_uri(). '/css/models'. $min .'.css',
                'version'    => $version
            )
        );
        
        /// THEME - Structure
        tify_register_style(
            'themeStructure',
            array(
                'src'        => get_stylesheet_directory_uri(). '/css/structure'. $min .'.css',
                'version'    => $version
            )
        );
        
        /// THEME - Article
        tify_register_style(
            'themeArticle',
            array(
                'src'        => get_stylesheet_directory_uri(). '/css/article'. $min .'.css',
                'version'    => $version
            )
        );
        
        /// THEME - Animation
        tify_register_style(
            'themeAnimation',
            array(
                'src'        => get_stylesheet_directory_uri(). '/css/animation'. $min .'.css',
                'version'    => $version
            )
        );
        
        /// THEME - Responsive
        tify_register_style(
            'themeResponsive',
            array(
                'src'        => get_stylesheet_directory_uri(). '/css/responsive'. $min .'.css',
                'version'    => $version
            )
        );
        
        /// THEME - Tronc commun
        tify_register_style(
            'themeRoot',
            array(
                'src'        => get_stylesheet_directory_uri(). '/style.css',
                'deps'        => array(
                    'themeFontGoogle',
                    'themeReset',
                    'themeHelpers',
                    'themeColors',
                    'themeElements',
                    'themeModels',
                    'themeStructure',
                    'themeArticle',
                    'themeAnimation',
                    'themeResponsive'
                ),
                'version'    => $version
            )
        );

        // SCRIPTS
        /// THEME - Tronc commun
        tify_register_script(
            'themeRoot',
            array(
                'src'           => get_stylesheet_directory_uri() .'/js/scripts'. $min .'.js',
                'deps'          => array(
                    'jquery'
                ),
                'version'       => $version,
                'in_footer'     => true
            )
        );
    }

    /**
     * Mise en file des scripts de l'interface utilisateur
     */
    public function enqueue_scripts()
    {
        // jQuery
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', '//code.jquery.com/jquery-3.1.1.min.js', '3.1.1', true );
        
        // Bootstrap
        wp_enqueue_style( 'bootstrap' );
        
        // Action de pré-chargement des scripts
        do_action( 'theme_before_enqueue_scripts' );
        
        wp_enqueue_style( 'themeRoot' );
        wp_enqueue_script( 'themeRoot' );
        
        // Action de post-chargement des scripts
        do_action( 'theme_after_enqueue_scripts' );
    }

    /**
     * Mise en file des scripts de l'interface administrateur
     */
    final public function admin_enqueue_scripts()
    {
        wp_enqueue_style( 'themeFontGoogle' );
    }

    /**
     * Styles de l'interface d'authentification 
     */
    final public function login_enqueue_scripts()
    {
    ?><link rel="stylesheet" id="custom_wp_admin_css"  href="<?php echo get_template_directory_uri();?>/css/login.css" type="text/css" media="all" /><style type="text/css">body.login div#login h1 a{background-image: url('data:image/svg+xml;base64,<?php echo base64_encode( file_get_contents( get_stylesheet_directory().'/images/logo.svg' ) );?>');}</style><?php
    }
}