<?php
/*
Addon Name: Imprint Page
Addon URI: http://presstify.com/policy/addons/imprint
Description: Page des mentions légales
Version: 1.0.1
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

class mktzr_imprint{
	var $path,
		$src;
	
	/**
	 * Initialisation
	 */
	function __construct(){
		//Définition
		$this->path = preg_replace( '/'.preg_quote( MKTZR_DIR, '/' ).'/', '', dirname( __FILE__ ) );
		$this->src 	= MKTZR_URL . $this->path;
		 
		add_action( 'admin_init', array( &$this, 'register_setting' ) );
	}
	
	/**
	 * Déclaration des options
	 */
	function register_setting(){
		register_setting( 'reading', 'page_for_imprint' );
		add_settings_section( 
			'mktzr_imprint_reading_section', 
			__( 'Mentions légales', 'bigben' ), 
			null,
			'reading' 
		);
		add_settings_field( 'page_for_imprint', __( 'Page d\'affichage des mentions légales', 'bigben' ), array( &$this, 'setting_field_render' ), 'reading', 'mktzr_imprint_reading_section' );
	}
	
	/**
	 * Rendu des options
	 */
	function setting_field_render(){
		wp_dropdown_pages( 
			array( 
				'name' => 'page_for_imprint', 
				'post_type' => 'page', 
				'selected' => get_option( 'page_for_imprint', false ), 
				'show_option_none' => __( 'Aucune page choisie', 'bigben' ), 
				'sort_column'  => 'menu_order' 
			) 
		);
	}
}
new mktzr_imprint;

/**
 * Affichage du lien vers les Mentions Légales
 */
function mktzr_imprint_display(){
	$page_for_imprint = get_option( 'page_for_imprint');
	$output  = "";
	$output .=  "<a href=\"". ( $page_for_imprint ? get_permalink( $page_for_imprint ) : '#' ) ."\""
				. " title=\"". sprintf( __( 'En savoir plus sur %s', 'tify' ), ( $page_for_imprint ? get_the_title( $page_for_imprint ) : __( 'Les mentions légales', 'tify' ) ) ) ."\">"
				. ( $page_for_imprint ? get_the_title( $page_for_imprint ) : __( 'Mentions légales', 'tify' ) )
				. "</a>";
	
	echo $output;
}