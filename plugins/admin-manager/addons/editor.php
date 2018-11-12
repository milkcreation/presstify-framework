<?php
/*
Addon Name: Editor Customizer
Addon URI: http://presstify.com/admin-manager/addons/editor-customizer
Description: Personnalisation de l'éditeur de texte Wordpress (tinyMCE)
Version: 1.0.1
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

/**
 * Première ligne des boutons tinymce
 */ 
function mkthm_mce_buttons( $buttons ){
	$buttons = array( 
		'bold', 'italic', 'underline', 'strikethrough', 'blockquote', '|',		
		'alignleft', 'aligncenter', 'alignright', 'alignjustify', '|',
		'bullist', 'numlist', 'outdent', 'indent', '|',
		'link', 'unlink', 'hr', /*'advlink', 'anchor', 'wp_more', */ '|',		
		'dfw', 'wp_adv'		
	);
	return $buttons;
}
add_filter( 'mce_buttons', 'mkthm_mce_buttons');
  
/**
 * Deuxième ligne des boutons tinymce
 */
function mkthm_mce_buttons_2( $buttons ) {
	$buttons = array(
		'undo', 'redo', '|',
		'pastetext', '|', 
		'styleselect', 'formatselect', '|', 'fontselect', 'fontsizeselect',  'forecolor', 'backcolor', '|', 'removeformat', '|',
		'subscript', 'superscript', 'charmap', '|'	  	
	);
	return $buttons;
}
add_filter( 'mce_buttons_2', 'mkthm_mce_buttons_2' ); 

/**
 * Troisième ligne des boutons tinymce (plugins additionnels)
 */ 
function mkthm_mce_buttons_3( $buttons ){
	if( ! function_exists( 'tify_tinymceplugins_get_active' ) || ! ( $plugins = tify_tinymceplugins_get_active() ) )
		return $buttons;
	foreach( $plugins as $plugin )
		array_push( $buttons, $plugin );
	
	return $buttons;		
}
add_filter( 'mce_buttons_3', 'mkthm_mce_buttons_3' );

/**
 * Personnalisation de la config de l'éditeur.
 */
function mkthm_mce_before_init( $mceInit ){	
	$mceInit['textcolor_map'] = '['.mkthm_colors_tinymce4_color_map().']';
	$mceInit['block_formats'] = apply_filters( 'mkthm_mce_block_formats', "Paragraphe=p;Paragraphe sans espace=div;Titre 3=h3;Titre 4=h4;Titre 5=h5;Titre 6=h6" );
	$mceInit['style_formats'] = json_encode( 
		apply_filters( 'mkthm_mce_style_formats',
			array( 
				array( 'title' => 'Alignement à Droite', 'selector' => 'p, span, img, a', 'classes' => 'alignleft' ),
				array( 'title' => 'Alignement au Centre', 'selector' => 'p, span, img, a', 'classes' => 'aligncenter' ),
				array( 'title' => 'Alignement à Gauche', 'selector' => 'p, span, img, a', 'classes' => 'alignright' ),
				array( 'title' => 'Alignement vertical en haut', 'selector' => 'span, img, a', 'classes' => 'aligntop' ),
				array( 'title' => 'Alignement vertical au milieu', 'selector' => 'span, img, a', 'classes' => 'alignmiddle' ),
				array( 'title' => 'Alignement vertical en bas', 'selector' => 'span, img, a', 'classes' => 'align' ),
				array( 'title' => 'Texte en majuscules', 'inline' => 'span', 'classes' => 'uppercase' ),
				array( 'title' => 'Bouton #1', 'inline' => 'span', 'classes' => 'button_primary' ),
				array( 'title' => 'Bouton #2', 'inline' => 'span', 'classes' => 'button_secondary' ),
				array( 'title' => 'Bouton #3', 'inline' => 'span', 'classes' => 'button_thirdary' )
			)
		)
	);	
   	$mceInit['fontsize_formats'] = apply_filters( 'mkthm_mce_fontsize_formats', "10px 11px 12px 13px 14px 16px 18px 20px 24px 28px 32px 36px 40px 44px 48px 52px 64px 128px 256px" );
	$mceInit['font_formats'] = apply_filters( 'mkthm_mce_font_formats', "Open Sans=Open Sans,sans-serif" ); // "Open Sans=Open Sans,sans-serif;Lora=lora,serif"
	
	return $mceInit;
}
add_filter( 'tiny_mce_before_init', 'mkthm_mce_before_init' );

/**
 * Désactive la suppression des paragraphes
 */
function mkthm_no_autop_htmledit( $output ) {
	$output = str_replace( array('&amp;', '&lt;', '&gt;'), array('&', '<', '>'), $output );
	$output = wpautop( $output );
	$output = htmlspecialchars( $output, ENT_NOQUOTES);
	
	return $output;
}
//add_filter( 'htmledit_pre', 'mkthm_no_autop_htmledit', 999 );

//remove_filter('the_content', 'wpautop');

/**
 * Couleurs du thème
 */
function mkthm_colors_hex(  ){
	return  apply_filters( 'mkthm_colors', array( 'Noir' =>  '#000000', 'Blanc' => '#FFFFFF' ) );
}

/**
 * 
 */
function mkthm_colors_tinymce4_color_map(  ){
	$colors =  mkthm_colors_hex( );
	$color_string = "";
	foreach( $colors as $name=> $hex )
		$color_string .= "\"".preg_replace( '/\#/', '', $hex )."\",\"$name\",";
	return $color_string;
}