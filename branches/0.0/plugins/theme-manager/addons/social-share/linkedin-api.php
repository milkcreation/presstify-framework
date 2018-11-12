<?php
class tiFy_SocialShare_Linkedin{
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
		$options = wp_parse_args( $options, array( 'linkedin' => $this->defaults ) );
		$this->options = $options['linkedin'];	
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation du thème == **/
	function wp_after_setup_theme(){
		if( ! $this->master->actives['linkedin'] )
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
				'id' 		=> 'site-options-sn-linkedin',
				'parent' 	=> 'tify_social_share',
				'title' 	=> __( 'Linkedin', 'tify' ),
				'cb' 		=> 'tiFy_SocialShare_Linkedin_Taboox',
				'order'		=> 5
			)
		);
	}
	/** == Déclaration des taboox == **/
	function tify_taboox_register_form(){		
		tify_taboox_register_form( 'tiFy_SocialShare_Linkedin_Taboox', $this );
	}
}

/* = TABOOX = */
/** == == **/
class tiFy_SocialShare_Linkedin_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	// Configuration
			$name = 'tify_social_share', 
			$defaults,
			// Référence
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_SocialShare_Linkedin $master ){
		$this->master = $master;
		$this->defaults['linkedin'] = $this->master->defaults; 
		
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
						<?php _e( 'Url du compte Linkedin', 'tify' );?><br>
					</th>
					<td>
						<input type="text" name="<?php echo $this->name;?>[linkedin][uri]" value="<?php echo $this->value['linkedin']['uri'];?>" size="80" placeholder="<?php _e( 'https://www.linkedin.com/profile/[nom du compte]', 'tify' );?>" />
					</td>
				</tr>
			</tbody>
		</table>
	<?php
	}
}

/**
 * lien vers la page
 */
function tify_lkin_api_page_link( $args = array() ){
	global $tify_social_share;

	$defaults = array(
			'class'		=> '',
			'title'		=> '',
			'attrs'		=> array(),
			'echo'		=> true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$output = "<a href=\"". $tify_social_share->lkin->options[ 'uri' ] ."\" class=\"$class\"";
	if( ! isset( $attrs['title'] ) )
		$output .= " title=\"". sprintf( __( 'Vers le compte Linkedin du site %s', 'tify'), get_bloginfo( 'name' ) ) ."\"";
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