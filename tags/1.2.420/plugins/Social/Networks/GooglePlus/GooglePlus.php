<?php
namespace tiFy\Plugins\Social\Networks\GooglePlus;

use tiFy\Plugins\Social\Factory;

class GooglePlus extends Factory
{
	/* = ARGUMENTS = */
	//
	protected static $OptionID 				= 'googleplus';
	
	// Liste des Actions à déclencher
	protected $tFyAppActions				= array(
		'init',
		'wp_enqueue_scripts',
		'wp_footer',
		'tify_options_register_node'
	);
	// Ordres de priorité d'exécution des actions
	protected $tFyAppActionsPriority	= array(
		'wp_footer' => 99
	);
		
	/* = DECLENCHEURS = */
	/** == Initialisation globale == **/
	final public function init()
	{		
		require_once self::tFyAppDirname() ."/Helpers.php";
		
		// Initialisation des scripts
		wp_register_style( 'tiFyPluginsSocialNetworksGooglePlusShare', self::tFyAppUrl() .'/Share.css', array(), '160511' );		
		wp_register_script( 'tiFyPluginsSocialNetworksGooglePlusShare', self::tFyAppUrl() .'/Share.js', array( 'jquery', 'tiFyPluginsSocialNetworksTwitterWidgets'  ), '160629', true );	
	}
	
	/** == Mise en file des scripts == **/
	final public function wp_enqueue_scripts()
	{
		wp_enqueue_style( 'tiFyPluginsSocialNetworksGooglePlusShare' );
		wp_enqueue_script( 'tiFyPluginsSocialNetworksGooglePlusShare' );
	}
	
	/** == Pied de page du site == **/
	final public function wp_footer()
	{
		?><script type="text/javascript">/* <![CDATA[ */
		      window.___gcfg = {
		        lang: '<?php echo get_locale();?>'
		      };		
		      (function() {
		        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		        po.src = 'https://apis.google.com/js/plusone.js';
		        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		      })();
	      /* ]]> */</script><?php		
	}

	/** == Déclaration d'un section de boîte à onglets == **/
	final public function tify_options_register_node(){
		tify_options_register_node(	
			array(
				'id' 		=> 'tify_social_share-gplus',
				'parent' 	=> 'tiFyPluginSocial',
				'title' 	=> "<i class=\"fa fa-google-plus\"></i> ". __( 'Google Plus', 'tify' ),
				'cb' 		=> "tiFy\\Plugins\\Social\\Networks\\GooglePlus\\Taboox\\Option\\PageLink\\Admin\\PageLink"	
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
		
		
		$output = 	"<a href=\"https://plus.google.com/share?url=". esc_attr( $url )."\"".
					" onclick=\"javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;\"".
					" class=\"$class\"".
					" data-tiFyPluginSocialNetwork=\"googleplus\">".
						"{$icon}{$text}".
					"</a>";
	
		if( $echo )
			echo $output;
		else
			return $output;
	}
}