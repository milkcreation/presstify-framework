<?php
/*
Addon Name: Cookie Policy
Addon URI: http://presstify.com/policy/addons/cookie_policy
Description: Politique des cookies
Version: 1.150312
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

class tify_cookie_policy{
	var $options,
		$path,
		$src;
	
	/**
	 * Initialisation
	 */
	function __construct(){
		//Définition
		$this->path = preg_replace( '/'.preg_quote( MKTZR_DIR, '/' ).'/', '', dirname( __FILE__ ) );
		$this->src 	= MKTZR_URL . $this->path;
			
		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'admin_init', array( $this, 'wp_admin_init' ) );		
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );		
		add_action( 'wp_ajax_tify_cookie_policy_set_cookie', array( $this, 'wp_ajax' ) );
		add_action( 'wp_ajax_nopriv_tify_cookie_policy_set_cookie', array( $this, 'wp_ajax' ) );
	}	
	
	/**
	 * Définition des options
	 */
	function set_options( ){
		$defaults = array( 
			'text' 			=> __( 'En poursuivant votre navigation sur ce site, vous acceptez l’utilisation de cookies pour vous proposer des services et offres adaptés à vos centres d’intérêts.', 'tify' ),
			'read_url'		=> get_permalink( get_option( 'page_for_cookie_policy' ) ),
			'close_text'	=> __( 'Fermer', 'tify' ),
			'cookie_expire'	=> YEAR_IN_SECONDS
		);
		$this->options = wp_parse_args( apply_filters( 'tify_cookie_policy_options', array() ), $defaults );				
	}	
	
	/**
	 * ACTIONS ET FILTRES WORDPRESS
	 */
	/**
	 * Initialisation globale
	 */
	function wp_init(){
		// Définition des options
		$this->set_options( );
		// Déclaration des scripts
		wp_register_script( 'tify-cookie_policy', $this->src.'/cookie_policy.js', array( 'jquery' ), '141118', true );
		wp_register_style( 'tify-cookie_policy', $this->src.'/cookie_policy.css', array( 'dashicons' ), '141118' );
	}
	
	/**
	 * Initialisation de l'interface d'administration
	 */
	function wp_admin_init(){
		register_setting( 'reading', 'page_for_cookie_policy' );
		add_settings_section( 
			'tify_cookie_policy_reading_section', 
			__( 'Politique des cookies', 'tify' ), 
			null,
			'reading' 
		);
		add_settings_field( 'page_for_cookie_policy', __( 'Page d\'affichage de la politique des cookies', 'tify' ), array( $this, 'setting_field_render' ), 'reading', 'tify_cookie_policy_reading_section' );		
	}
	
	/**
	 * Instanciation des scripts
	 */
	function wp_enqueue_scripts(){
		wp_enqueue_script( 'tify-cookie_policy' );
		wp_enqueue_style( 'tify-cookie_policy' );
	}
	
	/**
	 * Affichage
	 */
	function wp_footer(){
		$output  = "";
		$output .= "\t<div id=\"tify_cookie_policy\" style=\"". ( ( isset( $_COOKIE[ 'tify-cookie_policy' ] ) && ( $_COOKIE[ 'tify-cookie_policy' ] == true ) ) ? 'display:none;': '' ) ."\">\n";
		$output .= "\t\t<p>{$this->options['text']}</p>\n";
		$output .= "\t\t<a href=\"#tify_cookie_policy-accept\" id=\"tify_cookie_policy-accept\">". __( 'Accepter', 'tify' ) ."</a>&nbsp;&nbsp;";
		if( $this->options['read_url'] )
			$output .= "\t\t<a href=\"{$this->options['read_url']}\" id=\"tify_cookie_policy-read\" target=\"_blank\">". __( 'En savoir plus', 'tify' ) ."</a>\n";
		$output .= "\t\t<a href=\"#tify_cookie_policy-close\" id=\"tify_cookie_policy-close\" >{$this->options['close_text']}</a>\n";
		$output .= "\t</div>\n";
		
		echo apply_filters( 'tify_cookie_policy_display', $output, $this->options );
	}
	
	/**
	 * Definition du cookie
	 */
	function wp_ajax(){
		setcookie( 'tify-cookie_policy', true, time() + $this->options['cookie_expire'], SITECOOKIEPATH );
		wp_die(1);
	}
	
	/**
	 * VUES
	 */	
	/**
	 * Options/Lecture de l'interface d'administration
	 */
	function setting_field_render(){
		wp_dropdown_pages( 
			array( 
				'name' 				=> 'page_for_cookie_policy', 
				'post_type' 		=> 'page', 
				'selected' 			=> get_option( 'page_for_cookie_policy', false ), 
				'show_option_none' 	=> __( 'Aucune page choisie', 'tify' ), 
				'sort_column'  		=> 'menu_order' 
			) 
		);
	}
}
new tify_cookie_policy;