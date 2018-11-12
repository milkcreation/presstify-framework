<?php
/*
Addon Name: Landing Page
Addon URI: http://presstify.com/theme_manager/addons/landing_page
Description: 
Version: 1.0.1
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

/**
 * Verifie si la page d'attente doit être affichée
 */
function mkthm_landing_page_show_site(){
	if( ! $user = wp_get_current_user() )
		return;
	
	if( $allowed_users = apply_filters( 'mkthm_landing_page_allowed_users', array( ) ) )	
		return ( $allowed_users && ! in_array(  $user->user_login, $allowed_users ) );
	if( apply_filters( 'mkthm_landing_page_logged_in', true ) )
		return ( ! is_user_logged_in() );
}

/**
 * Mise en file d'attente des scripts
 */
function mkthm_landing_page_enqueue_scripts( ){
	if( ! mkthm_landing_page_show_site() )
		return;
	if( file_exists( get_template_directory().'/css/landing-page.css' ) )
		wp_enqueue_style( 'mkthm_landing-page', get_template_directory_uri().'/css/landing-page.css', array(), '140807' );	
	if( file_exists( get_template_directory().'/js/landing-page.js' ) )
		wp_enqueue_script( 'mkthm_landing-page', get_template_directory_uri().'/js/landing-page.js', array(), '140807' );	
}
add_action( 'wp_enqueue_scripts', 'mkthm_landing_page_enqueue_scripts' );

/**
 * Affichage de la page d'attente du site
 */
function mkthm_landing_page_template_redirect( ){
	if( ! mkthm_landing_page_show_site() )
		return;
	
	get_template_part( apply_filters( 'mktzr_landing_page_template', 'landing-page' ) );
	exit;
}
add_action( 'template_redirect', 'mkthm_landing_page_template_redirect' );