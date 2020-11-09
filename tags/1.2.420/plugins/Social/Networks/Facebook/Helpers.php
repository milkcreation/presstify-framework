<?php
/* = GENERAL TEMPLATE = */
/** == Bouton de partage Facebook == **/
function tify_social_facebook_share( $post = 0, $args = array(), $echo = true ){
	return \tiFy\Plugins\Social\Networks\Facebook\Facebook::ShareButton( $post, $args, $echo );
}

/** == Lien vers la page Facebook == **/
function tify_social_facebook_page_link( $args = array() )
{
	if( empty( \tiFy\Plugins\Social\Social::$Options['fb'][ 'uri' ] ) )
		return;

	$defaults = array(
		'class'		=> '',
		'text'		=> __( 'Page Facebook', 'tify' ),
		'attrs'		=> array(),
		'echo'		=> true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$output = "<a href=\"". ( \tiFy\Plugins\Social\Social::$Options['fb'][ 'uri' ] ) ."\" class=\"$class\"";
	if( ! isset( $attrs['title'] ) )
		$output .= " title=\"". sprintf( __( 'Vers la page Facebook du site %s', 'tify' ), get_bloginfo( 'name' ) ) ."\"";
	if( ! isset( $attrs['target'] ) )
		$output .= " target=\"_blank\"";
	
	foreach( (array) $attrs as $key => $value )
		$output .= " {$key}=\"{$value}\"";
	$output .= ">{$text}</a>";

	if( $echo ) :
		echo $output;
	else :
		return $output;
	endif;
}