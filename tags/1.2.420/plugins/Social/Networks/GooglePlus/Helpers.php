<?php
/** == Bouton de partage == **/
function tify_social_googleplus_share( $post = 0, $args = array(), $echo = true )
{
	return \tiFy\Plugins\Social\Networks\GooglePlus\GooglePlus::ShareButton( $post, $args, $echo );
}

/** == Lien vers la page == **/
function tify_social_googleplus_page_link( $args = array() )
{
	if( empty( \tiFy\Plugins\Social\Social::$Options['gplus'][ 'uri' ] ) )
		return;

	$defaults = array(
        'class'		=> '',
        'text'		=> __( 'Page Google +', 'tify' ),
        'attrs'		=> array(),
        'echo'		=> true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$output = "<a href=\"". tiFy\Plugins\Social\Social::$Options['gplus'][ 'uri' ] ."\" class=\"$class\"";
	if( ! isset( $attrs['text'] ) )
		$output .= " title=\"". sprintf( __( 'Vers la page Google+ du site %s', 'tify'), get_bloginfo( 'name' ) ) ."\"";
	
	if( ! isset( $attrs['target'] ) )
		$output .= " target=\"_blank\"";
	
	foreach( (array) $attrs as $key => $value )
		$output .= " {$key}=\"{$value}\"";
				
	$output .= ">$text</a>";

	if( $echo )
		echo $output;
	else
		return $output;
}