<?php
/*
Addon Name: Sanitize Posts
Addon URI: http://presstify.com/admin_manager/addons/sanitize-posts
Description: 
Version: 1.0.1
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

/**
 * Nettoyage des metaboxes du tableau de bord
 */
function mkthm_sanitize_posts_metaboxes() {
	remove_meta_box('dashboard_quick_press', 'dashboard', 'normal'); // quick press
}
add_action('admin_init', 'mkthm_sanitize_posts_metaboxes');

/**
 * Suppression du menu des articles
 */
function mkthm_sanitize_posts_menus(){
  remove_menu_page( 'edit.php' );  
}
add_action( 'admin_menu', 'mkthm_sanitize_posts_menus' );

/**
 * Désactivation de la métaboxes d'édition des articles dans l'interface de menu
 */
function mkthm_sanitize_posts_nav_menu_meta_box_object( $post_type ){
	if( $post_type->name == 'post' )
		return false;
	return $post_type;	
}
add_filter( 'nav_menu_meta_box_object', 'mkthm_sanitize_posts_nav_menu_meta_box_object' );

/**
 * Nettoyage de la barre d'administration
 */
function mkthm_sanitize_posts_remove_admin_bar_links() {
    global $wp_admin_bar;
	$wp_admin_bar->remove_node( 'new-post' );
}
add_action( 'wp_before_admin_bar_render', 'mkthm_sanitize_posts_remove_admin_bar_links' );