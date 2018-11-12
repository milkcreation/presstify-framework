<?php
/*
Plugin Name: Forms
Plugin URI: http://presstify.com/forms
Description: Gestion de formulaires
Version: 1.150305
Author: Milkcreation
Author URI: http://milkcreation.fr
Text Domain: tify_forms
*/

/**
 * Usage :	
	array(
		'slug'			=> 'lastname',
		'label' 		=> __( 'Nom', '[theme_textdomain]' ),
		'placeholder'	=> __( 'Saisissez votre nom de famille', '[theme_textdomain]' ),
		'type' 			=> 'input',
		'html' 			=> '',
		'default'		=> '',
		'required'		=> true	
	),
	array(
		'slug'			=> 'email',
		'label' 		=> __( 'Adresse email', '[theme_textdomain]' ),
		'placeholder'	=> __( 'Renseignez votre adresse email', '[theme_textdomain]' ),
		'type' 			=> 'input',
		'html' 			=> '',
		'default'		=> '',
		'required'		=> true,
		'integrity_cb'	=> 'is_email',
		'add-ons' => array(
			'record' => array( 'column' => true )
		)	
	),
	array(
		'slug'			=> 'message',
		'label' 		=> __( 'Message', '[theme_textdomain]' ),
		'placeholder'	=> __( 'Votre message', '[theme_textdomain]' ),
		'type' 			=> 'textarea',
		'html' 			=> '',
		'default'		=> '',
		'required'		=> true		
	)
	...
 */

class tiFy_forms{
	var 	
		// Chemins 
		$dir,
		$uri,
		// Formulaires enregistrés
		$forms = array(),
		//
		$mkcf, 
		$field_types, 
		$addons,
		$dirs;
	
	/**
	 * Initialisation
	 */
	function __construct( $args = array() ){
		$defaults = array(
			'dirs'			=> array( ),
			'addon_actives'	=> array( 'dashboard', 'cookie_transport', 'mailer', 'record', 'user' )
		);
		$args = wp_parse_args( $args, $defaults );
				
		// Définition des chemins
		$this->dir 		= dirname( __FILE__ );
		$this->uri		= plugin_dir_url(__FILE__);
		
		// Initialisation de la classe de formulaire.
		if( ! class_exists( 'MKCF' ) ) :
			global $tiFy;
			require_once  $tiFy->dir .'/inc/milk_form_class/milk_form_class.php';
		endif;
		$this->mkcf = New MKCF( 'tiFy_forms' );
		
		// Déclaration des chemins du répertoire d'upload
		$this->dirs = array(
			'temp'		=> array(
				'dirname'	=> WP_CONTENT_DIR. '/uploads/tify_forms/temp',
				'cleaning'	=> true
			),
			'upload'		=> array(
				'dirname'	=> WP_CONTENT_DIR. '/uploads/tify_forms/upload',
			),
			'export'		=> array(
				'dirname'	=> WP_CONTENT_DIR. '/uploads/tify_forms/export',
				'cleaning'	=> 3600
			)
		);
		$this->mkcf->dirs_init( $this->dirs );
	
		// Déclaration des types de champs	
		$this->field_types = array(
			$this->dir .'/field-type/dynamic/dynamic.php',
			$this->dir .'/field-type/file/file.php',
			$this->dir .'/field-type/plupload/plupload.php',			
			$this->dir .'/field-type/simple-captcha-image/simple-captcha-image.php',
			$this->dir .'/field-type/tify_dropdown/tify_dropdown.php',
			$this->dir .'/field-type/touchtime/touchtime.php'
		);
		if( version_compare( phpversion(), '5.3', '<' ) ) 
			array_push( $this->field_types, $this->dir .'/field-type/recaptcha/recaptcha.old.php' );
		else
			array_push( $this->field_types, $this->dir .'/field-type/recaptcha/recaptcha.php' );
		
		/// Initialisation des types de champs
		$this->mkcf->types_init( $this->field_types );		
		/// Instanciation des types de champs
		new tify_forms_field_dynamic( $this->mkcf );
		new tify_forms_field_file( $this->mkcf );
		new tify_forms_field_plupload( $this->mkcf );		
		new tify_forms_field_recaptcha( $this->mkcf );
		new tify_forms_field_simple_captcha_image( $this->mkcf );
		new tify_forms_field_tify_dropdown( $this->mkcf );
		new tify_forms_field_touchtime( $this->mkcf );		
		
		// Déclaration des addons
		$this->addons = array(
			'dashboard' => array(
				'path' => dirname(__FILE__).'/add-ons/dashboard/dashboard.php'
			),
			/*'ajax_submit' => array(
					'path' => dirname(__FILE__).'/add-ons/ajax_submit/ajax_submit.php'
			),*/
			'cookie_transport' => array(
				'path' => $this->dir .'/add-ons/cookie_transport/cookie_transport.php'
			),
			'mailer' => array(
				'path' => $this->dir .'/add-ons/mailer/mailer.php'
			),
			'record' => array(
					'path' => $this->dir .'/add-ons/record/record.php'
			),
			'user' => array(
				'path' => $this->dir .'/add-ons/user/user.php'
			)
		);
		// Désactivation des addons non déclarés
		$this->addons = array_intersect_key( $this->addons, array_flip ( $args['addon_actives'] ) );
		
		/// Initialisation des addons
		$this->mkcf->addons_init( $this->addons );
		/// Instanciation des addons
		if( in_array( 'dashboard', $args['addon_actives'] ) )
			new tify_forms_addon_dashboard( $this->mkcf );
		if( in_array( 'cookie_transport', $args['addon_actives'] ) )
			new tify_forms_addon_cookie_transport( $this->mkcf );
		if( in_array( 'mailer', $args['addon_actives'] ) )
			new tify_forms_addon_mailer( $this->mkcf );
		if( in_array( 'record', $args['addon_actives'] ) )
			new tify_forms_addon_record( $this->mkcf );
		if( in_array( 'user', $args['addon_actives'] ) )
			new tify_forms_addon_user( $this->mkcf );
		
		// Actions et Filtres Wordpress
		add_action( 'admin_init', array( $this, 'init_forms' ), 9 );
		add_action( 'wp', array( $this, 'init_forms' ), 9 );	
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */	
	/** == Initialisation des formulaires == **/
	function init_forms(){
		do_action( 'tify_form_register' );
		// Initialisation des formulaires (requis)
		$this->mkcf->forms_init( $this->forms );
		// Traitement des formulaires en soumission (requis)
		$this->mkcf->handle->proceed();
	}
	
	/* = VUES = */	
	/** == Affichage du formulaire == **/
	function display( $form_id = null, $echo = false ){
		if( is_null( $form_id ) )	
			$form_id = 1;
		
		// Traitement des options du formulaire	
		$output  = "";
		$output .= "\n<div id=\"tify_form-{$form_id}\" class=\"tify_form\">";
		$output .= $this->mkcf->forms->display( $form_id, $echo );
		$output .= "\n</div>";

		return $output;
	}
	
	/* = CONTRÔLEUR = */
	/** == Déclaration d'un formulaire == **/
	function register_form( $form = array() ){
		array_push( $this->forms, $form );
		
		return $form['ID'];
	}
}
global $tify_forms;
$tify_forms = new tiFy_forms;

/* = Affichage d'un formulaire = */
function tify_form_display( $form = null, $echo = true ){
	if( $echo )
		echo do_shortcode( '[formulaire id="'. $form .'"]' );
	else
		return do_shortcode( '[formulaire id="'. $form .'"]' );
}

/* = Declaration des formulaire = */
function tify_form_register( $form = array() ){
	global $tify_forms;
	
	return $tify_forms->register_form( $form );
}

/** == Shortcode d'appel du formulaire == **/
add_shortcode( 'formulaire', 'tify_form_shortcode' );
function tify_form_shortcode( $atts = array() ){
	global $tify_forms;
		
	extract( 
		shortcode_atts(
			array( 'id' => null ), 
			$atts
		) 
	);

	return $tify_forms->display( $id, false );
}