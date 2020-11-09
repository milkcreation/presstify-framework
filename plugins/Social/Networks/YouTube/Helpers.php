<?php
/** == lien vers la page == **/
function tify_social_youtube_page_link( $args = array() )
{
	if( empty( tiFy\Plugins\Social\Social::$Options['youtube'][ 'uri' ] ) )
		return;

	$defaults = array(
		'class'		=> '',
		'text'		=> '',
		'attrs'		=> array(),
		'echo'		=> true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$output = "<a href=\"". tiFy\Plugins\Social\Social::$Options['youtube'][ 'uri' ] ."\" class=\"$class\"";
	
	if( ! isset( $attrs['title'] ) )
		$output .= " title=\"". sprintf( __( 'Vers la chaîne YouTube du site %s', 'tify'), get_bloginfo( 'name' ) ) ."\"";
		
	if( ! isset( $attrs['target'] ) )
		$output .= " target=\"_blank\"";
	
	foreach( (array) $attrs as $key => $value )
		$output .= " {$key}=\"{$value}\"";
	
	$output .= ">{$text}</a>";

	if( $echo )
		echo $output;
	else
		return $output;
}