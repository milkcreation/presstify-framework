<?php

/**
 * 
 */
function tify_deprecated_function( $function ){
	ob_start();
	trigger_error( sprintf( 'Vous devriez activer la fonction %s', $function ) );
	//ob_end_flush();
	ob_end_clean();
}

/* = PLUGGABLE = */
/** == Extrait == **/
if ( ! function_exists( 'mk_chars_based_excerpt' ) ) : 
function mk_chars_based_excerpt( $string, $args = array() ){
	tify_deprecated_function( 'tify_excerpt' );
	return tify_excerpt( $string, $args );
}
endif; 

/**
 * 
 */
function tify_deprecated_plugin( $plugin ){
	ob_start();
	trigger_error( sprintf( 'Vous devriez activer le plugin %s', $plugin ) );
	//ob_end_flush();
	ob_end_clean();
}


if( ! function_exists( 'register_postbox' ) ) :
function register_postbox(){
	return tify_deprecated_plugin( 'postbox' );
}
endif;

if( ! function_exists( 'mkpbx_add_section' ) ) :
function mkpbx_add_section(){
	return tify_deprecated_plugin( 'postbox' );
}
endif;

if( ! function_exists( 'mktzr_p4a_get_permalink_structure' ) ) :
function mktzr_p4a_get_permalink_structure(){
	return tifY_deprecated_plugin( 'post4archive' );
}
endif;

if( ! function_exists( 'get_the_subtitle' ) ) :
function get_the_subtitle(){
	return tifY_deprecated_plugin( 'postbox' );
}
endif;