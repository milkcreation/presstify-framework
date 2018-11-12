<?php
/*
Addon Name: Video Player
Addon URI: http://presstify.com/theme-manager/addons/video_player
Description: Lecteur de vidéo
Version: 1.0.1
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

/**
 * RESSOURCES
 * @see https://css-tricks.com/rundown-of-handling-flexible-media/
 */

/* = HELPERS = */
/** == Mise en file des scripts == **/
function tify_video_enqueue(){
	wp_enqueue_style( 'tify_video_player' );
	wp_enqueue_script( 'tify_video_player' );
}

/** == Lien vers une vidéo == **/
function tify_video_link( $attr = array(), $args = array() ){
	static $instance;
	$instance ++;
	
	$defaults_atts = array(
		'src'      => '',
		'poster'   => '',
		'loop'     => '',
		'autoplay' => '',
		'preload'  => 'metadata',
		'width'    => '100%',
		'height'   => '100%',
	);
	$attr = wp_parse_args( $attr, $defaults_atts );
	
	$defaults = array(
		'id' 		=> 'tify_video_link-'. $instance,
		'class'		=> '',
		'href'		=> '',
		'html'		=> '',
		'attrs'		=> '',
		'echo'		=> true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$output  = "";
	$output .= "<a href=\"". ( $href ? $href : $attr['src'] ) ."\"";
	$output .= " id=\"{$id}\" class=\"tify_video_link {$class}\" data-tify_video=\"1\"";
	foreach( $attr as $k => $v )
		$output .= " data-{$k}=\"{$v}\"";
	foreach( $attrs as $i => $j )
		$output .= " {$i}=\"{$j}\"";
	$output .= ">";	
	$output .= $html;
	$output .= "</a>";
	
	if( $echo )
		echo $output;
	else	
		return $output;
}
 
 
class tiFy_Video{
	/* = ARGUMENTS = */
	public	// Chemins
			$dir,
			$uri;
	
	/* = CONSTRUCTEUR = */
	function __construct(){
		// Définition des chemins
		$this->dir = dirname( __FILE__ );
		$this->uri = plugin_dir_url( __FILE__ );
		
		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'wp_head', array( $this, 'wp_head' ) );
		add_filter( 'embed_oembed_html', array( $this, 'wp_embed_oembed_html' ), null, 4 ) ;
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
		
		add_action( 'wp_ajax_tify_video', array( $this, 'wp_ajax_action' ) );
		add_action( 'wp_ajax_nopriv_tify_video', array( $this, 'wp_ajax_action' ) );
		
		add_filter( 'wp_video_extensions', array( $this, 'wp_video_extensions' ) );
	}	
		
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation globale == **/
	function wp_init(){
		wp_register_style( 'tify_video_player', $this->uri. 'tify_video.css', array( 'wp-mediaelement', 'dashicons', 'spinkit-three-bounce' ), '20141222' );
		wp_register_script( 'tify_video_player', $this->uri .'tify_video.js', array( 'jquery', 'froogaloop', 'wp-mediaelement' ), '20141222', true );
	}
	
	/** == Responsivité des vidéos intégrées == **/
	function wp_head(){
	?><style type="text/css">.tify_video-embedded{position:relative;padding-bottom:56.25%;padding-top:30px;height:0;overflow:hidden;} .tify_video-embedded object,.tify_video-embedded iframe,.tify_video-embedded video,.tify_video-embedded embed{max-width:100%;position:absolute;top:0;left:0;width:100%;height:100%;}</style><?php
	}
		
	/** == Encapsulation des vidéo intégrées == **/
	function wp_embed_oembed_html( $html, $url, $attr, $post_ID ) {
	    $return = '<div class="tify_video-embedded">'. $html .'</div>';
	    return $return;
	}
		
	/** == Pied de page du site == **/
	function wp_footer(){
	?>
		<div id="tify_video-modal">
			<div id="tify_video-overlay">
				<div id="tify_video-spinner" class="sk-spinner sk-spinner-three-bounce">
					<div class="sk-bounce1"></div>
					<div class="sk-bounce2"></div>
					<div class="sk-bounce3"></div>
				</div>
				<div id="tify_video-wrapper"></div>
			</div>
		</div>
	<?php
	}
	
	/** == == */
	function wp_ajax_action(){
		if( empty( $_REQUEST['attr']['src'] ) )
			die(0);
		
		$attr = $_REQUEST['attr'];
		
		$src = preg_replace( '/'. preg_quote( site_url(), '/' ) .'/',  '', $attr['src'] );
		
		$output = "";
		if( $output = wp_oembed_get(  $src, $attr ) ) :		
			$output = "<div class=\"tify_video-container tify_video-embedded\">". $output ."</div>";
		else :			
			$_attr = '';
			foreach( $_REQUEST['attr'] as $k => $v )
				$_attr .= "$k='$v' ";
			$output = "<div class=\"tify_video-container tify_video-shortcode\">". do_shortcode( "[video $_attr]" ) ."</div>";
		endif;
		
		echo $output;
		exit;
	}
	
	/** == == **/
	function wp_video_extensions( $ext ){
		array_push( $ext, 'mov' );		
		return $ext;
	}
}
new tiFy_Video;