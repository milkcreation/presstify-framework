<?php
/** == Bouton de partage ==
 * @see https://dev.twitter.com/web/tweet-button
 */
function tify_social_twitter_share( $post = 0, $args = array(), $echo = true )
{
	return \tiFy\Plugins\Social\Networks\Twitter\Twitter::ShareButton( $post, $args, $echo );
}

/** == lien vers la page == **/
function tify_social_twitter_page_link( $args = array() )
{
	if( empty( \tiFy\Plugins\Social\Social::$Options['tweet'][ 'uri' ] ) )
		return;

	$defaults = array(
			'class'		=> '',
			'text'		=> __( 'Compte Twitter', 'tify' ),
			'title'		=> '',
			'attrs'		=> array(),
			'echo'		=> true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$output = "<a href=\"". ( \tiFy\Plugins\Social\Social::$Options['tweet'][ 'uri' ] ) ."\" class=\"$class\"";
	if($title == '' ) {
        $output .= " title=\"". sprintf( __( 'Vers le compte Twitter du site %s', 'tify' ), get_bloginfo( 'name' ) ) ."\"";
    } else {
        $output .= " title=\"". sprintf( $title, get_bloginfo( 'name' ) ) ."\"";
    }
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