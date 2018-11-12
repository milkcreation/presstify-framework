<?php
/*
Addon Name: SMTP
Addon URI: http://presstify.com/core/addons/smtp
Description: Utilisation d'un serveur d'envoi SMTP
Version: 1.150410
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

/**
	USAGE :

 	add_filter( 'tify_smtp_settings', '{function_hook_name}' );
	function function_hook_name( ){
		return array(
			'host'			=> '',
			'port'			=> 25,
			'username'		=> '',
			'password'		=> '',
			'smtp_auth'		=> true,
			'smtp_secure'	=> false,
			'from_email'	=> '',
 			'from_name'		=> '',
 			'from_force'	=> false	
		); 
	}
 */

/**
 * @see https://gist.github.com/franz-josef-kaiser/5840282
 */ 
class tiFy_SMTP{
	public $settings;
	
	function __construct(){
		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		add_filter( 'wp_mail_from', array( $this, 'wp_mail_from' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'wp_mail_from_name' ) );
		add_action( 'phpmailer_init', array( $this, 'phpmailer_init' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation globale de Wordpress == **/
	function wp_init(){
		// Traitement des réglages du compte
		$defaults = array(
			'host'			=> '',
			'port'			=> 25,
			'username'		=> '',
			'password'		=> '',
			'smtp_auth'		=> true,
			'smtp_secure'	=> false,
			'from_email'	=> get_option( 'admin_email' ),
 			'from_name'		=> ( $admin_user = get_user_by( 'email', get_option( 'admin_email' ) ) ) ? $admin_user->display_name : get_bloginfo( 'name' ),
 			'from_force'	=> false
		);
		$this->settings = wp_parse_args( apply_filters( 'tify_smtp_settings', array() ), $defaults );	
	}
	
	/** == Email d'expédition == **/
	function wp_mail_from( $email ){
		// Bypass
		if( $email && ! preg_match( '/^wordpress@.*$/', $email ) && ! $this->settings['from_force'] )
			return $email;
		
		if( $this->settings['from_email'] && is_email( $this->settings['from_email'] ) )
			$email = $this->settings['from_email'];
		
		return $email;
	}
	/** == Nom de l'expéditeur == **/
	function wp_mail_from_name( $name ){
		// Bypass
		if( $name && ! preg_match( '/^wordpress$/i', $name ) && ! $this->settings['from_force'] )
			return $name;
			
		if( $this->settings['from_name'] )
			$name = $this->settings['from_name'];
		
		return $name;	
	}
	
	/* = CONTRÔLEURS = */
	/** ==  == **/
	function phpmailer_init( PHPMailer $phpmailer ) {
	    $phpmailer->Host 		= $this->settings['host'];
	    $phpmailer->Port 		= $this->settings['port'];
	    $phpmailer->Username 	= $this->settings['username'];
	    $phpmailer->Password 	= $this->settings['password'];
	    $phpmailer->SMTPAuth 	= $this->settings['smtp_auth'];
		if( $this->settings['smtp_secure'] ) 
	    	$phpmailer->SMTPSecure = $this->settings['smtp_secure']; // ssl | tls
	
	    $phpmailer->IsSMTP();
	}
}
new tiFy_SMTP;