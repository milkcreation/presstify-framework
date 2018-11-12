<?php
/*
Plugin Name: PresstiFy
Plugin URI: http://presstify.com/
Description: Framework Wordpress par Milkcreation
Version: 1.150417
Author: Milkcreation
Author URI: http://milkcreation.fr
Text Domain: tify
*/

// @TODO A Supprimer à terme
define( 'MKTZR_DIR', dirname(__FILE__) );
define( 'MKTZR_URL', plugin_dir_url(__FILE__) );

class tiFy{
	/* = ARGUMENTS = */
	public	// Chemins
			$dir,
			$path,
			$uri,
			
			// 			
			$plugin_data,
			
			// Habilitations
			$allowed_users 	= array(), // login des utilisateurs habilités aux fonctions avancées des administrateurs
			$capability,
		
			// Contrôleurs
			$dashboard,
			$ajax_actions,
			$plugins,
			$options;
	
	/**
	 * Initialisation
	 */
	function __construct(){
		global $tiFy;
		
		$tiFy = $this;
		
	 	$this->dir = dirname(__FILE__);
	 	$this->path = preg_replace( '/'. preg_quote( ABSPATH, '/' ) .'/', '', $this->dir );
		$this->uri = site_url( '/'. $this->path );
		
		// Chargement des post-contrôleurs (modifiables par les extensions et les modules)
		
		require_once( $this->dir.'/inc/pluggable.php' );
		
		// Chargement des contrôleurs primaires
		/// Contrôleur de données en base
		require_once $this->dir .'/inc/db.php';
		/// Contrôleur de champs
		require_once( $this->dir.'/inc/tify_controls/tify_controls.php' );
		/// Contrôleur de boîtes à onglets
		require_once $this->dir .'/inc/tify_tabooxes/tify_tabooxes.php';
		/// Contrôleur des posts d'accroche
		require_once $this->dir .'/inc/tify_hook_for_archive/tify_hook_for_archive.php';
		/// Contrôleur des options
		require_once $this->dir .'/inc/tify_options/tify_options.php';
		/// Contrôleur des vidéos
		require_once $this->dir .'/inc/tify_video/tify_video.php';		
		
		// Chargement des contrôleurs secondaires
		/*require_once( $this->dir.'/inc/dashboard.php' );
			$this->dashboard = new tiFy_dashboard( $this );*/
		require_once( $this->dir.'/inc/ajax-actions.php' );
			$this->ajax_actions = new tiFy_ajax_actions( $this );
		require_once( $this->dir.'/inc/plugins.php' );
			$this->plugins = new tiFy_plugins( $this );
		// @TODO A refaire
		//require_once( $this->dir.'/inc/options.php' );
			//$this->options = new tiFy_options( $this );	
			
		// Chargement des extensions extensions et des addons actifs
		$this->plugins->load_active_plugins_with_addons();
		
		require_once( $this->dir.'/inc/deprecated.php' );
		
		// Actions
		add_action( 'init', array( $this, 'wp_init' ), 9 );
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_enqueue_scripts' ) );
		add_filter( 'map_meta_cap', array( $this, 'wp_map_meta_cap' ), 99, 4 );
	}
	
	/**
	 * Initialisation du plugin
	 */
	function wp_init(){
		global $locale, $wp_local_package;
		$_locale = preg_split( '/_/', $locale );
		
		// Translation
		load_textdomain( 'tify', $this->dir.'/languages/tify-'. $locale .'.mo' );
		
		// Données du plugin
		$this->plugin_data = get_plugin_data( __FILE__ );
		
		// Habilitations
		$initial_allowed_user = array();
		if( $this->admin_user = get_user_by( 'email', get_option( 'admin_email' ) ) )
			array_push( $initial_allowed_user, $this->admin_user->user_login );		
		$this->allowed_users 	= apply_filters( 'tify_allowed_users', $initial_allowed_user );
		$this->capability 		= apply_filters( 'tify_capability', 'manage_tify' );
		
	  	// Déclaration des scripts
	  	/// CDN 
	  	//// Bootstrap
		wp_register_script( 'bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js', array( 'jquery' ), '3.3.4', true );
	  	//// FontAwesome
	  	wp_register_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), '4.3.0' );
		//// HighCharts
		wp_register_script( 'highcharts-core', '//cdnjs.cloudflare.com/ajax/libs/highcharts/4.1.5/highcharts.js', array( 'jquery' ), '4.1.5' );
		//// Moment
		wp_register_script( 'momentjs', '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js', array(), '2.10.2', true );
		//// Bootstrap datetimepicker
		//wp_register_style( 'datetimepicker', $this->uri .'/assets/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css', array(), '4.7.14' );
		//wp_register_script( 'datetimepicker', $this->uri .'/assets/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js', array( 'jquery', 'bootstrap', 'momentjs' ), '4.7.14', true );
	  	//// Spectrum
		wp_register_style( 'spectrum', '//cdnjs.cloudflare.com/ajax/libs/spectrum/1.7.0/spectrum.min.css', array( ), '1.7.0' );
	  	wp_register_script( 'spectrum', '//cdnjs.cloudflare.com/ajax/libs/spectrum/1.7.0/spectrum.min.js', array( 'jquery' ), '1.7.0', true );
		//if( file_exists( $this->dir .'/assets/js/bgrins-spectrum/i18n/jquery.spectrum-'. $_locale[0] .'.js' ) )
		wp_register_script( 'spectrum-i10n', '//cdnjs.cloudflare.com/ajax/libs/spectrum/1.7.0/i18n/jquery.spectrum-'. $_locale[0] .'.js', array( ), '1.7.0', true );
		// LOCAL
		/// SpinKit
		//// Complete
	  	wp_register_style( 'spinkit', $this->uri .'/assets/spinkit/spinkit.css', array(), '1.0' );
		wp_register_style( 'spinkit-rotating-plane', $this->uri .'/assets/spinkit/spinners/1-rotating-plane.css', array(), '1.0' );
		wp_register_style( 'spinkit-fading-circle', $this->uri .'/assets/spinkit/spinners/10-fading-circle.css', array(), '1.0' );
		wp_register_style( 'spinkit-double-bounce', $this->uri .'/assets/spinkit/spinners/2-double-bounce.css', array(), '1.0' );
		wp_register_style( 'spinkit-wave', $this->uri .'/assets/spinkit/spinners/3-wave.css', array(), '1.0' );
		wp_register_style( 'spinkit-wandering-cubes', $this->uri .'/assets/spinkit/spinners/4-wandering-cubes.css', array(), '1.0' );
		wp_register_style( 'spinkit-pulse', $this->uri .'/assets/spinkit/spinners/5-pulse.css', array(), '1.0' );
		wp_register_style( 'spinkit-chasing-dots', $this->uri .'/assets/spinkit/spinners/6-chasing-dots.css', array(), '1.0' );
		wp_register_style( 'spinkit-three-bounce', $this->uri .'/assets/spinkit/spinners/7-three-bounce.css', array(), '1.0' );
		wp_register_style( 'spinkit-circle', $this->uri .'/assets/spinkit/spinners/8-circle.css', array(), '1.0' );
		wp_register_style( 'spinkit-cube-grid', $this->uri .'/assets/spinkit/spinners/9-cube-grid.css', array(), '1.0' );
		wp_register_style( 'spinkit-wordpress', $this->uri .'/assets/spinkit/spinners/9-wordpress.css', array(), '1.0' );
		
		/// TiFY
	  	wp_register_style( 'tify_styles', $this->uri .'/assets/css/tify_styles.css', array(), '150409' );
		//// Find Post
		wp_register_script( 'tify-findposts', $this->uri .'/assets/js/tify-findposts.js', array( 'jquery', 'jquery-ui-draggable', 'wp-ajax-response' ), '2.2.2', true );
		//// Lightbox
		wp_register_script( 'tify-lightbox', $this->uri .'/assets/js/tify-lightbox.js', array( 'jquery' ), '150325', true );
		//// Smooth Anchor
		wp_register_script( 'tify-smooth-anchor', $this->uri .'/assets/js/tify-smooth-anchor.js', array( 'jquery' ), '150329', true );
		//// Slideshow
		wp_register_script( 'milk-slideshow', $this->uri .'/assets/js/milk-slideshow.js', array( 'jquery' ), '141218', true );	
	}
	
	/**
	 * Menu d'administration
	 */
	function wp_admin_menu(){
	  	add_menu_page( __( 'PresstiFy', 'tify' ) , __( 'PresstiFy', 'tify' ), $this->capability, 'tify', null, null, 66 );
	}
	
	/**
	 * Mise en file des scripts
	 */
	function wp_admin_enqueue_scripts(){
		wp_enqueue_style( 'tify_styles' );
	}
	
	/**
	 * Modification des habiltations
	 */
	function wp_map_meta_cap( $caps, $cap, $user_id, $args ){
		$user = get_userdata( $user_id );
		switch ( $cap ) :
			case 'activate_plugins' :	
			case 'update_core' :	
			case 'update_plugins' :
			case 'update_themes' :
			case 'install_plugins' :
			case 'install_themes' :
			case 'delete_plugins' :
			case 'delete_themes' :						
			case 'switch_themes':
			case 'edit_plugins' :
			case 'edit_themes' :
				if( ! $user || ! in_array(  $user->user_login, $this->allowed_users ) )			
					$caps = array( 'do_not_allow' );	
				break;
			case 'manage_tify' :
				if( $user && in_array(  $user->user_login, $this->allowed_users ) ) :
					$caps = array( 'exist' );
				else :
					$caps = array( 'do_not_allow' );
				endif;
				break;
			default :
					$caps = apply_filters( 'tify_map_meta_cap', $caps, $cap, $user_id, $args, $this );
				break;	
		endswitch;	

		return $caps;
	}	
	
	/**
	 * CONTRÔLEUR
	 */
	/**
	 * 
	 */
	function is_allowed_user( $user_id = 0 ){
		if( ! $user_id )
			$user_id =  get_current_user_id();
		
		if( $userdata = get_userdata( $user_id ) )
			return in_array( $userdata->user_login, $this->allowed_users );
		
		return false;
	}
	/**
	 * Récupération du chemin relatif d'un 
	 */
	function get_relative_path( $filename ){
		$filename = wp_normalize_path( $filename );	
		if( ( $path = preg_replace( '/'. preg_quote( $this->dir, '/' ) .'/', '', $filename ) ) && file_exists( $this->dir . $path ) )	
			return $path;
	}
}
global $tiFy;
$tiFy = new tiFy;

/* = HELPER = */
/** == Récupération des librairies == **/
function tify_require( $require ){
	if( ! in_array( $require, array( 'admin_view', 'csv', 'custom_column', 'mailer', 'mandrill', 'taxonomy_metadata' ) ) )
		return;
	
	global $tiFy;
	
	$filename = $tiFy->dir .'/lib/tify_'. $require .'/tify_'. $require .'.php'; 

	if( ! file_exists( $filename ) )
		return;
	require_once $filename;	
}

/**
 *
 */
function tify_enqueue_findposts(){
	static $instance;
	if( $instance++ )
		return;
	wp_enqueue_script( 'tify-findposts' );
    add_action( 'admin_footer', function() {
        echo "<div id=\"ajax-response\"></div>";
        find_posts_div();
    });
}

/**
 * 
 */
function tify_progress_enqueue(){
	static $instance;
	if( $instance++ )
		return;

	add_action( 'wp_footer', 'tify_progress_wp_footer' ); 
	add_action( 'admin_footer', 'tify_progress_wp_footer' ); 
}
function tify_progress_wp_footer(){
	echo 	"<div id=\"tify_progress\">\n".
			"\t<h3 class=\"title\"></h3>\n".
			"\t<div class=\"content-bar\">\n".
			"\t\t<div class=\"progress-bar\"></div>\n".
			"\t\t<div class=\"text-bar\">\n".
			"\t\t\t<span class=\"current\"></span><span class=\"sep\"></span><span class=\"total\"></span>\n".
			"\t\t</div>\n".
			"\t\t<div class=\"infos\"></div>\n".				
			"\t</div>\n".
			"</div>\n".
			"<div id=\"tify_overlay\"></div>";	
}