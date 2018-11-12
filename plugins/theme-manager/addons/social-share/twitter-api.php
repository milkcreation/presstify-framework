<?php
class tiFy_SocialShare_Twitter{
	/* = ARGUMENTS = */
	public	// Configuration
			$options,
			$defaults = array(
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
		$options = wp_parse_args( $options, array( 'tweet' => $this->defaults ) );
		$this->options = $options['tweet'];	
	}	
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation du thème == **/
	function wp_after_setup_theme(){
		if( ! $this->master->actives['twitter'] )
			return;
		
		// Définition des options par défaut
		$this->set_options();
		
		// Action et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );	
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		
		// Actions et Filtres PressTiFy
		add_action( 'tify_taboox_register_node', array( $this, 'tify_taboox_register_node' ) );	
		add_action( 'tify_taboox_register_form', array( $this, 'tify_taboox_register_form' ) );
	}
	
	/** == Initialisation globale == **/
	function wp_init(){		
		// Initialisation des scripts
		wp_register_script( 'tify_social_share_twitter_widgets', 'https://platform.twitter.com/widgets.js', array( ), '20150109', false );
		wp_register_script( 'tify_social_share_twitter', $this->master->uri.'/twitter-api-share.js', array( 'jquery', 'tify_social_share_twitter_widgets' ), '20150109', true );
	}
	
	/** == Mise en file des scripts == **/
	function wp_enqueue_scripts(){
		wp_enqueue_script( 'tify_social_share_twitter' );
	}
	
	/* = ACTIONS ET FILTRES PRESSTIFY = */
	/** == Déclaration d'une section de boîte à onglets == **/
	function tify_taboox_register_node(){
		tify_options_register_node(	
			array(
				'id' 		=> 'site-options-sn-tweet',
				'parent' 	=> 'tify_social_share',
				'title' 	=> __( 'Twitter', 'tify' ),
				'cb' 		=> 'tiFy_SocialShare_Twitter_Taboox',
				'order'		=> 2
			)
		);
	}
	/** == Déclaration des taboox == **/
	function tify_taboox_register_form(){		
		tify_taboox_register_form( 'tiFy_SocialShare_Twitter_Taboox', $this );
	}
}

/* = TABOOX = */
/** == == **/
class tiFy_SocialShare_Twitter_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	// Configuration
			$name = 'tify_social_share', 
			$defaults,
			// Référence
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_SocialShare_Twitter $master ){
		$this->master = $master;
		$this->defaults['tweet'] = $this->master->defaults; 
		
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
						<?php _e( 'Url du compte Twitter', 'tify' );?>
					</th>
					<td>
						<input type="text" name="<?php echo $this->name;?>[tweet][uri]" value="<?php echo $this->value['tweet']['uri'];?>" size="80" placeholder="<?php _e( 'https://twitter.com/[nom de la page]', 'tify' );?>" />
					</td>
				</tr>
			</tbody>
		</table>
	<?php
	}
}

/* = GENERAL TEMPLATE = */
/** == Bouton de partage ==  
 * @see https://dev.twitter.com/web/tweet-button
 */
function tify_tweet_api_share_button( $args = array() ){
	$defaults = array(
		'class'				=> '',
		'button_text'		=> '',
		'url'				=> wp_get_shortlink( ),
		'text'				=> is_singular() ? get_the_title( get_the_ID() ) : get_bloginfo( 'name' ),
		'echo'				=> true	
	);	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$output = "<a href=\"https://twitter.com/intent/tweet?url=".esc_attr( $url ) ."&text=".esc_attr( $text ) ."\" class=\"$class\">$button_text</a>";
	
	if( $echo )
		echo $output;
	else
		return $output;
}

/** == lien vers la page == **/
function tify_tweet_api_page_link( $args = array() ){
	global $tify_social_share;

	if( empty( $tify_social_share->tweet->options[ 'uri' ] ) )
		return;

	$defaults = array(
			'class'		=> '',
			'text'		=> '',
			'attrs'		=> array(),
			'echo'		=> true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$output = "<a href=\"". $tify_social_share->tweet->options[ 'uri' ] ."\" class=\"$class\"";
	if( ! isset( $attrs['title'] ) )
		$output .= " title=\"". sprintf( __( 'Vers le compte Twitter du site %s', 'tify' ), get_bloginfo( 'name' ) ) ."\"";
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