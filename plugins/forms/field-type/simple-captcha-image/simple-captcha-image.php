<?php
/**
 * @name FORMS - SIMPLE CAPTCHA IMAGE
 * @description Champs de formulaire simple captcha image
 * 
 * @package Milk_Thematzr
 * @subpackage Forms
 * 
 * @usage
 * 
	array(
		'ID' => #,
		'title' => 'Sample de Formulaire',
		'prefix' => 'sample',
		'fields' => array(
			array(
				'slug'			=> 'captcha',
				'type'			=> 'simple-captcha-image'
			)
 	... 
 * 	Modification de l'image de texture du captcha :
	add_filter( "mktzr_forms_sci_background_image", create_function( '', "return '".MKTZR_URL."/plugins/forms/field-type/simple-captcha-image/texture.jpg';" ) );
 * 
 */

class tify_forms_field_simple_captcha_image{
	public 	// Classe parente
			$mkcf,
			
			// Chemins
			$dir, $path, $uri;
	
	/**
	 * Initialisation
	 */
	function __construct( MKCF $mkcf ){
		// MKCF	
		$this->mkcf = $mkcf;
		// Définition des chemins
		$this->dir 	= dirname( __FILE__ );
		$this->uri	= plugin_dir_url( __FILE__ );
		
		// Déclaration du type de champs
		$this->set_type();	
			
		// Actions et Filtres Wordpress		
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'wp_ajax_mktzr_forms_sci_get_image', array( $this, 'wp_ajax' ) );
				
		// Callbacks
		$this->mkcf->callbacks->field_type_set( 'field_set', 'simple-captcha-image', array( $this, 'cb_field_set' ) );
		$this->mkcf->callbacks->field_type_set( 'field_type_output_display', 'simple-captcha-image', array( $this, 'cb_field_type_output_display' ) );
		$this->mkcf->callbacks->field_type_set( 'handle_check_request', 'simple-captcha-image', array( $this, 'cb_handle_check_request' ) );	
	}
	
	/**
	 * Déclaration du type de champs
	 */
	function set_type(){
		// Déclaration du type de champ
		$this->mkcf->fields->set_type( 
			array( 
				'slug'			=> 'simple-captcha-image',
				'label' 		=> __( 'Captcha Image', 'mk_form_class' ),
				'section' 		=> 'misc',
				'supports'		=> array( 'label', 'request', 'integrity-cb' )
			)
		);	
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation globale == **/
	function  wp_init(){
		// Initialisation des sessions
		if( ! isset( $_SESSION ) )
			@ session_start();
		if( ! isset( $_SESSION['security_number'] ) )
			$_SESSION['security_number'] = rand( 10000, 99999 );
	}
	
	/** == RECUPERATION DE L'IMAGE VIA AJAX == **/
	function wp_ajax(){	
		echo "<img src=\"". $this->uri. "/image.php" . "\" alt=\"".__( 'Captcha Introuvable', 'tify' )."\" style=\"float:left;\" />";
		exit;
	}
	
	
	/* = CALLBACKS MKCF = */		
	/** == Attribut de champ requis obligatoire == **/
	function cb_field_set( &$field ){
		// Bypass
		if( $field['type'] != 'simple-captcha-image' )
			return;	
		
		$field['required'] = true;
	}
	
	/** == Affichage du captcha == **/
	function cb_field_type_output_display( &$output, $field ){
		// Bypass
		if( $field['type'] != 'simple-captcha-image' )
			return;	

		$img =	$this->uri. "image.php";
		// Afficher l'image dans l'iframe : add_query_arg( array( 'action' => 'mktzr_forms_sci_get_image' ), admin_url( 'admin-ajax.php' ) )
		// L'iframe permet de forcer l'appel de l'url
		$output .= "<iframe src='". $img ."' style=\"display:none\"></iframe>";
		$output .= "<img src=\"". $img ."\" alt=\"".__( 'captcha introuvable', 'tify' )."\" style=\"float:left;\" />";
		$output .= "<input type=\"text\" name=\"". esc_attr( $this->mkcf->fields->get_name( $field ) ) ."\" value=\"\" size=\"8\" autocomplete=\"off\" placeholder=\"".__( 'code de sécu.', 'tify')."\" />";
	}
	
	/**
	 * Vérification des données du champ au moment du traitement de la requête
	 */
	function cb_handle_check_request( &$errors, $field ){
		// Bypass
		if( $field['type'] != 'simple-captcha-image' )
			return;
		if( ! $this->mkcf->handle->parsed_request['fields'][ $field['slug'] ] )
			return;

		if( ! isset( $_SESSION['security_number'] ) ) :
			$errors[] = __( 'ERREUR SYSTÈME : Impossible de définir le code de sécurité' );
		elseif( $this->mkcf->handle->parsed_request['fields'][ $field['slug'] ]['value'] != $_SESSION['security_number'] ) :
			$errors[] = __( 'La valeur du champs de sécurité doit être identique à celle de l\'image', 'tify' );
		endif;
		
		$_SESSION['security_number'] = rand( 10000, 99999 );
			
		return;
	}	
}