<?php
namespace tiFy\Plugins\Social\Networks\Twitter;

use tiFy\Plugins\Social\Factory;

class Twitter extends Factory
{
	/* = ARGUMENTS = */
	//
	protected static $OptionID 			= 'twitter';
	
	// Liste des Actions à déclencher
	protected $tFyAppActions				= array(
		'init',
		'wp_enqueue_scripts',
		'tify_options_register_node'
	);
	
	/* = DECLENCHEURS = */
	/** == Initialisation globale == **/
	final public function init()
	{		
		require_once self::tFyAppDirname() ."/Helpers.php";
		
		// Initialisation des scripts
		wp_register_style( 'tiFyPluginsSocialNetworksTwitterShare', self::tFyAppUrl() .'/Share.css', array(), '160511' );	
		wp_register_script( 'tiFyPluginsSocialNetworksTwitterWidgets', '//platform.twitter.com/widgets.js', array( ), '20150109', true );		
		wp_register_script( 'tiFyPluginsSocialNetworksTwitterShare', self::tFyAppUrl() .'/Share.js', array( 'jquery', 'tiFyPluginsSocialNetworksTwitterWidgets'  ), '160629', true );	
	}
	
	/** == Mise en file des scripts == **/
	final public function wp_enqueue_scripts()
	{
		wp_enqueue_style( 'tiFyPluginsSocialNetworksTwitterShare' );
		wp_enqueue_script( 'tiFyPluginsSocialNetworksTwitterShare' );
	}
	
	/* = ACTIONS ET FILTRES PRESSTIFY = */
	/** == Déclaration d'une section de boîte à onglets == **/
	final public function tify_options_register_node()
	{
		tify_options_register_node(	
			array(
				'id' 		=> 'tify_social_share-tweet',
				'parent' 	=> 'tiFyPluginSocial',
				'title' 	=> "<i class=\"fa fa-twitter\"></i> ". __( 'Twitter', 'tify' ),
				'cb' 		=> "\\tiFy\\Plugins\\Social\\Networks\\Twitter\\Taboox\\Option\\PageLink\\Admin\\PageLink"	
			)
		);
	}
	
	/* = AFFICHAGE = */
	/** == Lien de partage == **/
	public static function ShareButton( $post = 0, $args = array(), $echo = true )
	{
		global $wp;
		
		static $Instance; $Instance++;
		
		if( ! ( $post = get_post( $post ) ) && is_singular() )
			$post = get_post();	
	
		$defaults = array(
			'id'				=> '',
			'class'				=> '',
			'text'				=> false,
			'icon'				=> true,
			'url'				=> ! empty( $post ) ? wp_get_shortlink( $post->ID ) : home_url( add_query_arg( array(), $wp->request ) ),
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );
		
		if( $icon && is_bool( $icon ) )
			$icon = \tiFy\Lib\Utils::get_svg( self::tFyAppDirname() .'/assets/logo.svg', false );
		
		
		$output = 	"<a href=\"https://twitter.com/intent/tweet?url=".esc_attr( $url ) ."&text=".esc_attr( $text ) ."\"".
					" class=\"$class\"".
					" data-tiFyPluginSocialNetwork=\"twitter\">".
						"{$icon}{$text}".
					"</a>";
	
		if( $echo )
			echo $output;
		else
			return $output;
	}
}