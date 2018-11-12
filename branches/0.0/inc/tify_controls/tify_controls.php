<?php
/**
 * CLASSE MAITRESSE
 */
class tiFy_controls{
	/* = CONTRUCTEUR = */
	function __construct(){
		$this->dir 		= dirname( __FILE__ );
		// Instanciation des contrôleurs
		require_once $this->dir ."/tify_control/checkbox/checkbox.php";
		$this->checkbox = new tify_control_checkbox( $this );
		
		require_once $this->dir ."/tify_control/colorpicker/colorpicker.php";
		$this->colorpicker = new tify_control_colopicker( $this );

		require_once $this->dir ."/tify_control/dropdown/dropdown.php";
		$this->dropdown = new tify_control_dropdown( $this );
		
		require_once $this->dir ."/tify_control/dropdown_colors/dropdown_colors.php";
		$this->dropdown_colors = new tify_control_dropdown_colors( $this );
		
		require_once $this->dir ."/tify_control/dropdown_glyphs/dropdown_glyphs.php";
		$this->dropdown_glyphs = new tify_control_dropdown_glyphs( $this );
		
		require_once $this->dir ."/tify_control/dropdown_images/dropdown_images.php";
		$this->dropdown_images = new tify_control_dropdown_images( $this );
		
		require_once $this->dir ."/tify_control/dropdown_menu/dropdown_menu.php";
		$this->dropdown_menu = new tify_control_dropdown_menu( $this );
		
		require_once $this->dir ."/tify_control/dynamic_inputs/dynamic_inputs.php";
		$this->dynamic_inputs = new tify_control_dynamic_inputs( $this );
		
		require_once $this->dir ."/tify_control/media_image/media_image.php";
		$this->media_image = new tify_control_media_image( $this );
		
		require_once $this->dir ."/tify_control/switch/switch.php";
		$this->switch = new tiFy_Control_Switch_Class( $this );
		
		require_once $this->dir ."/tify_control/text_remaining/text_remaining.php";
		$this->text_remaining = new tify_control_text_remaining( $this );
		
		require_once $this->dir ."/tify_control/touch_time/touch_time.php";
		$this->touch_time = new tify_control_touch_time( $this );
	}
}
global $tiFy_controls;
$tiFy_controls = new tiFy_controls;

/**
 * CLASSE DE FABRICATION
 */
class tify_control{
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_controls $master ){
		global $tiFy;

		$this->tiFy 	= $tiFy;
		// Définition des chemins
		$this->path  	= $this->tiFy->get_relative_path( $this->dir );
		$this->uri		= $this->tiFy->uri . $this->path;
		
		add_action( 'init', array( $this, 'wp_init' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation globale == **/
	function wp_init(){
		$this->register_scripts();
	}
	
	/* = METHODES = */
	/** == Déclaration des scripts == **/
	function register_scripts(){}
	/** == Mise en file des scripts == **/
	function enqueue_scripts(){}	
	/** == Affichage du controleur == **/
	function display( $args = array() ){}
}


/**
 * UTILITAIRES
 */
/**
 * Mise en file des scripts
 */
function tify_controls_enqueue( $scripts ){
	global $tiFy_controls;
	
	if( is_string( $scripts ) )
		$scripts = array( $scripts );
		
	foreach( $scripts as $script ) :
		$tiFy_controls->$script->enqueue_scripts();
		if( wp_style_is( 'tify_controls-'.$script, 'registered' ) ) 
			wp_enqueue_style( 'tify_controls-'.$script );
		if( wp_script_is( 'tify_controls-'.$script, 'registered' ) ) 
			wp_enqueue_script( 'tify_controls-'.$script );
	endforeach;				
}