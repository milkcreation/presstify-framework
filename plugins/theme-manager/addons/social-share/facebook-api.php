<?php
class tiFy_SocialShare_Facebook{
	/* = ARGUMENTS = */
	public	// Configuration
			$options,
			$defaults = array(
				'appId' 	=> '',
				'uri' 		=> ''
			),
			// Référence
			$master;
		
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_SocialShare $master ){
		$this->master = $master;
		
		// Actions et Filtres Wordpress
		add_action( 'after_setup_theme', array( $this, 'wp_after_setup_theme' ) );
	}
	
	/* = CONFIGURATION = */
	/** == Définition des options == **/
	function set_options(){
		$options = get_option( 'tify_social_share' );
		$options = wp_parse_args( $options, array( 'fb' => $this->defaults ) );
		$this->options = $options['fb'];	
	}	
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation du thème == **/
	function wp_after_setup_theme(){
		if( ! $this->master->actives['facebook'] )
			return;
		
		// Définition des options
		$this->set_options();
				
		// Action et Filtres Wordpress		
		add_action( 'init', array( $this, 'wp_init' ) );		
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_filter( 'language_attributes', array( $this, 'wp_language_attributes' ) );
		add_action( 'wp_head', array( $this, 'wp_head' ) );
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
		
		// Actions et Filtres PressTiFy
		add_action( 'tify_taboox_register_node', array( $this, 'tify_taboox_register_node' ) );	
		add_action( 'tify_taboox_register_form', array( $this, 'tify_taboox_register_form' ) );
	}
	
	/** == Initialisation globale == **/
	function wp_init(){	
		// Initialisation des scripts
		wp_register_script( 'tify_social_share_facebook', $this->master->uri.'/facebook-api.js', array( 'jquery' ), '20150108', true );	
	}
	
	/** == Mise en file des scripts == **/
	function wp_enqueue_scripts(){
		wp_enqueue_script( 'tify_social_share_facebook' );
	}	
	
	/** == Langage Attribute de la balise HTML  == **/
	function wp_language_attributes( $output ){
		if( is_admin() )
			return $output;
		return $output . ' xmlns:fb="http://www.facebook.com/2008/fbml"';
	}
		
	/** == Modification de l'entête du site == **/
	function wp_head(){
		if( $this->options['appId'] )
			echo '<meta content="'. $this->options['appId'] .'" property="fb:app_id">';
	}	
	
	/** == Modification du pied de page du site == **/
	function wp_footer(){
		// Bypass
		if( ! $this->options['appId'] )
			return;
		
		$src = ( @get_headers( 'http://connect.facebook.net/'. get_locale() .'/sdk.js' ) ) ? '//connect.facebook.net/'. get_locale() .'/sdk.js' : '//connect.facebook.net/fr_FR/sdk.js';
		?><div id="fb-root"></div><script type="text/javascript">/* <![CDATA[ */
		  window.fbAsyncInit = function() {
	        FB.init({
	          appId      : '<?php echo $this->options['appId'];?>',
	          xfbml      : true,
	          version    : 'v2.0'
	        });
	      };
	      (function(d, s, id){
	         var js, fjs = d.getElementsByTagName(s)[0];
	         if (d.getElementById(id)) return;
	         js = d.createElement(s); js.id = id;
	         js.src = "<?php echo $src;?>";
	         fjs.parentNode.insertBefore(js, fjs);
	       }(document, 'script', 'facebook-jssdk'));
		/* ]]> */</script><?php		
	}
	
	/* = ACTIONS ET FILTRES PRESSTIFY = */
	/** == Déclaration d'un section de boîte à onglets == **/
	function tify_taboox_register_node(){
		tify_options_register_node(	
			array(
				'parent' 	=> 'tify_social_share',
				'id' 		=> 'tify_social_share-facebook',				
				'title' 	=> __( 'Facebook', 'tify' ),
				'cb' 		=> 'tiFy_SocialShare_Facebook_Taboox',
				'order'		=> 1						
			)
		);
	}
	
	/** == Déclaration des taboox == **/
	function tify_taboox_register_form(){		
		tify_taboox_register_form( 'tiFy_SocialShare_Facebook_Taboox', $this );
	}
}

/* = TABOOX = */
/** == == **/
class tiFy_SocialShare_Facebook_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	// Configuration
			$name = 'tify_social_share', 
			$defaults,
			// Référence
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_SocialShare_Facebook $master ){
		$this->master = $master;
		$this->defaults['fb'] = $this->master->defaults; 
		
		parent::__construct(
			array(
				'environnements'	=> array( 'option' ),
				'dir'				=> dirname( __FILE__ ),
				'instances'  		=> 1
			)
		);		
	}
	
	/* = INTERFACE D'ADMIN = */
	/** == Formulaire de saisie == **/
	function form( $args = array() ){
	?>
		<table class="form-table">
			<tbody>			
				<tr>
					<th scope="row">
						<?php _e( 'Identifiant de l\'API Facebook', 'tify' );?>*<br>
						<em style="font-size:11px; color:#999;"><?php _e( 'Requis', 'tify' );?></em>	
					</th>
					<td>
						<input type="text" name="<?php echo $this->name;?>[fb][appId]" value="<?php echo $this->value['fb']['appId'];?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e( 'Url de la page Facebook', 'tify' );?><br>
					</th>
					<td>
						<input type="text" name="<?php echo $this->name;?>[fb][uri]" value="<?php echo $this->value['fb']['uri'];?>" size="80" placeholder="<?php _e( 'https://www.facebook.com/[nom de la page]','tify' );?>" />
					</td>
				</tr>
			</tbody>
		</table>
	<?php
	}
}

/* = GENERAL TEMPLATE = */
/** == Bouton de partage Facebook == **/
function tify_fb_api_share_button( $args = array() ){
	global $tify_social_share;
	
	$defaults = array(
		'class'		=> '',
		'uri'		=> is_singular() ? get_permalink( get_the_ID() ) : home_url(),
		'image'		=> ( is_singular() && ( $attachment_id = get_post_thumbnail_id( get_the_ID() ) ) ) ? $attachment_id : ( isset( $tify_social_share->options['og'][ 'default_image' ] ) ? $tify_social_share->options['og'][ 'default_image' ] : ''), 
		'title'		=> ( is_singular() ) ? get_the_title( get_the_ID() ) : get_bloginfo( 'name' ),
		'desc'		=> ( is_singular() ) ? get_the_excerpt() : get_bloginfo( 'description' ),
		'echo'		=> true	
	);	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	if( ( $img = tify_custom_attachment_image( $image, array( 1200, 630, true ) ) ) && ( $image_src = $img['url'] .'/'. $img['file'] ) ) :
	elseif( ( $img = tify_custom_attachment_image( $image, array( 600, 315, true ) ) ) && ( $image_src = $img['url'] .'/'. $img['file'] )  ) :
	elseif( isset( $attachment_id ) ):
		$image_src = wp_get_attachment_url( $attachment_id );
	endif;
	
	$output = "<a href=\"". esc_url( $uri )."\" class=\"{$class}\" data-action=\"tify-fb-api_share_button\" data-url=\"{$uri}\" data-title=\"". esc_attr( $title ). "\" data-desc=\"". esc_attr( $desc ) ."\" data-image=\"". esc_attr( $image_src ) ."\"></a>";
	
	if( $echo )
		echo $output;
	else
		return $output;
}

/** == Lien vers la page Facebook == **/
function tify_fb_api_page_link( $args = array() ){
	global $tify_social_share;
	
	if( empty( $tify_social_share->fb->options[ 'uri' ] ) )
		return;
	
	$defaults = array(
			'class'		=> '',
			'title'		=> '',
			'attrs'		=> array(),
			'echo'		=> true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$output = "<a href=\"". $tify_social_share->fb->options[ 'uri' ] ."\" class=\"$class\"";
	if( ! isset( $attrs['title'] ) )
		$output .= " title=\"". sprintf( __( 'Vers la page Facebook du site %s', 'tify' ), get_bloginfo( 'name' ) ) ."\"";
	if( ! isset( $attrs['target'] ) )
		$output .= " target=\"_blank\"";
	foreach( (array) $attrs as $key => $value ) 
		$output .= " {$key}=\"{$value}\"";
	$output .= ">$title</a>";

	if( $echo )
		echo $output;
	else
		return $output;
}