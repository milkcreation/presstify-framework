<?php
/*
 Addon Name: Social Share
 Addon URI: http://presstify.com/theme_manager/addons/social-share
 Description: Partage sur les réseaux sociaux
 Version: 1.150701
 Author: Milkcreation
 Author URI: http://milkcreation.fr
 */

class tiFy_SocialShare{
	/* = ARGUMENTS = */
	public	// Chemins
			$dir,
			$uri,
			
			// Configuration
			$actives,
			$options,
			
			// Contrôleurs
			$fb,
			$tweet,
			$gplus,
			$youtube,
			$instagram,
			$lkin;
	
	/* = CONSTRUCTEUR = */
	function __construct(){
		// Définition des chemins
		$this->dir = dirname( __FILE__ );
		$this->uri = plugin_dir_url( __FILE__ );	
				
		// Chargement des contrôleurs
		require_once( $this->dir .'/facebook-api.php' );
		$this->fb = new tiFy_SocialShare_Facebook( $this );
		
		require_once( $this->dir .'/twitter-api.php' );			
		$this->tweet = new tiFy_SocialShare_Twitter( $this );

		require_once( $this->dir .'/google-plus-api.php' );
		$this->gplus = new tiFy_SocialShare_GooglePlus( $this );

		require_once( $this->dir .'/youtube-api.php' );
		$this->youtube = new tiFy_SocialShare_YouTube( $this );
		
		require_once( $this->dir .'/linkedin-api.php' );
		$this->lkin = new tiFy_SocialShare_Linkedin( $this );
		
		require_once( $this->dir .'/instagram-api.php' );
		$this->instagram = new tiFy_SocialShare_Instagram( $this );		
		
		// Actions et Filtres Wordpress
		add_action( 'after_setup_theme', array( $this, 'wp_after_setup_theme' ), 9  );
		
		// Actions et Filtres PressTiFy
		add_action( 'tify_taboox_register_node', array( $this, 'tify_taboox_register_node' ) );
	}
	
	/* = CONFIGURATION = */
	/** == Définition des api actives == **/
	function set_actives(){
		return $this->actives = wp_parse_args( 
			apply_filters( 'tify_social_share_actives', array() ), 
			array( 
				'facebook' 		=> true,
				'twitter'		=> true,
				'googleplus'	=> false,
				'youtube'		=> false,
				'linkedin'		=> false,
				'instagram'		=> false				
			)
		);
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation du thème == **/
	function wp_after_setup_theme(){
		$this->set_actives();
	}
	
	/* = ACTIONS ET FILTRES PRESSTIFY = */
	/** == Déclaration de la boîte à onglets == **/
	function tify_taboox_register_node(){
		tify_options_register_node(
			array(
				'id' 		=> 'tify_social_share',
				'title' 	=> __( 'Réseaux sociaux', 'tify' ),
			)
		);
	}
}
global $tify_social_share;
$tify_social_share = new tiFy_SocialShare;