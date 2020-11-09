<?php
/** == lien vers la page == **/
function tify_social_linkedin_page_link( $args = array() )
{
	if( empty( tiFy\Plugins\Social\Social::$Options['linkedin'][ 'uri' ] ) )
		return;

	$defaults = array(
		'class'		=> '',
		'text'		=> '',
		'title'     => '',
		'attrs'		=> array(),
		'echo'		=> true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$output = "<a href=\"". tiFy\Plugins\Social\Social::$Options['linkedin'][ 'uri' ] ."\" class=\"$class\"";

	if($title == '') {
        $output .= " title=\"". sprintf( __( 'Vers le compte Linkedin du site %s', 'tify'), get_bloginfo( 'name' ) ) ."\"";
    }
	else {
	    $output .= " title=\"". sprintf( $title, get_bloginfo( 'name' ) ) ."\"";
	}
		
	if( ! isset( $target ) )
		$output .= " target=\"_blank\"";
	
	foreach( (array) $attrs as $key => $value )
		$output .= " {$key}=\"{$value}\"";
	
	$output .= ">{$text}</a>";

	if( $echo )
		echo $output;
	else
		return $output;
}