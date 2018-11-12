<?php
class tiFy_SocialShare_YouTube{
	/* = ARGUMENTS = */
	public	// Configuration
			$options,
			$defaults = array(
				'uri' 		=> ''
			),
			// Référence
			$master;
		
	/* = CONSTRUCTEUR = */
	function __construct( $master ){
		$this->master = $master;
		
		// Actions et Filtres Wordpress
		add_action( 'after_setup_theme', array( $this, 'wp_after_setup_theme' ) );		
	}
	
	/* = CONFIGURATION = */
	/** == Définition des options == **/
	function set_options(){
		$options = get_option( 'tify_social_share' );
		$options = wp_parse_args( $options, array( 'youtube' => $this->defaults ) );
		$this->options = $options['youtube'];	
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation du thème == **/
	function wp_after_setup_theme(){
		if( ! $this->master->actives['youtube'] )
			return;	
		
		// Définition des options par défaut
		$this->set_options();
		
		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		
		// Actions et Filtres PressTiFy
		add_action( 'tify_taboox_register_node', array( $this, 'tify_taboox_register_node' ) );
		add_action( 'tify_taboox_register_form', array( $this, 'tify_taboox_register_form' ) );
	}
	
	/** == Initialisation globale == **/
	function wp_init(){}
	
	/* = ACTIONS ET FILTRES PRESSTIFY = */
	/** == Déclaration d'un section de boîte à onglets == **/
	function tify_taboox_register_node(){
		tify_options_register_node(	
			array(
				'id' 		=> 'site-options-sn-youtube',
				'parent' 	=> 'tify_social_share',
				'title' 	=> __( 'YouTube', 'tify' ),
				'cb' 		=> 'tiFy_SocialShare_YouTube_Taboox',
				'order'		=> 4
			)
		);
	}
	/** == Déclaration des taboox == **/
	function tify_taboox_register_form(){		
		tify_taboox_register_form( 'tiFy_SocialShare_YouTube_Taboox', $this );
	}
}

/* = TABOOX = */
/** == == **/
class tiFy_SocialShare_YouTube_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	// Configuration
			$name = 'tify_social_share', 
			$defaults,
			// Référence
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_SocialShare_YouTube $master ){
		$this->master = $master;
		$this->defaults['youtube'] = $this->master->defaults; 
		
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
						<?php _e( 'Url de la chaîne You Tube', 'tify' );?><br>
					</th>
					<td>
						<input type="text" name="<?php echo $this->name;?>[youtube][uri]" value="<?php echo $this->value['youtube']['uri'];?>" size="80" placeholder="<?php _e( 'https://www.youtube.com/channel/[nom de la chaîne]', 'tify' );?>" />
					</td>
				</tr>
			</tbody>
		</table>
	<?php
	}
}

/* = GENERAL TEMPLATE = */
/** == lien vers la page == **/
function tify_youtube_api_page_link( $args = array() ){
	global $tify_social_share;
	
	if( empty( $tify_social_share->youtube->options[ 'uri' ] ) )
		return;
	
	$defaults = array(
			'class'		=> '',
			'title'		=> '',
			'attrs'		=> array(),
			'echo'		=> true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$output = "<a href=\"". $tify_social_share->youtube->options[ 'uri' ] ."\" class=\"$class\"";
	if( ! isset( $attrs['title'] ) )
		$output .= " title=\"". sprintf( __( 'Vers la chaîne YouTube+ du site %s', 'tify'), get_bloginfo( 'name' ) ) ."\"";
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