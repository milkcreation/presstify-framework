<?php
class tiFy_Options{
	/* = ARGUMENTS = */
	public 	// Chemins
			$dir,
			$uri,
			
			// Configuration
			$page_title,
			$menu_title,
			$page,
			$hookname,
			
			// Paramètres
			$register_settings = array(),
			$nodes = array(),
			$options;
			
	
	/* = CONSTRUCTEUR = */
	function __construct(){
		// Définition des chemins
		$this->dir = dirname( __FILE__ );
		$this->uri = plugin_dir_url( __FILE__ );
		
		// Configuration
		$this->page_title =  __( 'Réglages des options du thème', 'tify' );
		$this->menu_title = __( 'Thème', 'tify' );
		$this->admin_bar_title = false;		
		$this->page		= 'tify_theme_options';
		$this->hookname = 'settings_page_'. $this->page;		
				
		// Actions et Filtres Wordpress
		add_action( 'admin_bar_menu', array( $this, 'wp_admin_bar_menu' ) ); 
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'wp_admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_enqueue_scripts' ) );
		
		// Actions et Filtres PressTiFy
		add_action( 'tify_taboox_register_box', array( $this, 'tify_taboox_register_box' ) );
		add_action( 'tify_taboox_register_node', array( $this, 'tify_taboox_register_node' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Barre d'administration == **/
	function wp_admin_bar_menu( $wp_admin_bar ){
		// Bypass
		if( is_admin() )
			return;	
		$wp_admin_bar->add_node(
			array(
				'id' 		=> $this->hookname,
	    		'title'	 	=> ( $this->admin_bar_title ) ? $this->admin_bar_title : $this->page_title,
	    		'href' 		=> admin_url( '/options-general.php?page='. $this->page ),
	   			'parent' 	=> 'site-name'
			)
		);
	}
	
	/** == Menu d'administration == **/
	function wp_admin_menu(){
		add_options_page( $this->page_title, $this->menu_title, 'manage_options', $this->page, array( $this, 'admin_render' ) );		
	}
	
	/** == Initialisation de l'interface d'administration == **/
	function wp_admin_init(){
		// Déclaration des options
		$this->register_settings = array_unique( $this->register_settings );

		foreach( (array) $this->register_settings as $setting )
			register_setting( $this->page, $setting );
	}
	
	/** == Instanciation des scripts de l'administration == **/
	function wp_admin_enqueue_scripts( $hook_suffix ){
		// Bypass
		if( get_current_screen()->id != $this->hookname )
			return;
			
		do_action( 'tify_theme_options_enqueue_scripts' );
	}
	
	/* = ACTIONS ET FILTRES PressTiFy = */
	/** == Déclaration de la boîte à onglets == **/
	function tify_taboox_register_box(){
		tify_taboox_register_box_option( $this->hookname, 
			array(
				'title'		=> $this->page_title,
				'page'		=> $this->page
			)
		);
	}
	
	/** == Déclaration des sections de boîte à onglets == **/
	function tify_taboox_register_node(){
		foreach( (array) $this->nodes as $node )
			tify_taboox_register_node_option(
				$this->hookname,
				$node
			);
	}
	
	/* = TEMPLATES = */
	/** == Rendu de l'interface d'administration == **/
	function admin_render(){
	?>		
		<div class="wrap">
			<h2><?php echo $this->page_title;?></h2>
			<form method="post" action="options.php">
				<div style="margin-right:300px; margin-top:20px;">
					<div style="float:left; width: 100%;">
						<?php settings_fields( $this->page );?>	
						<?php do_settings_sections( $this->page );?>
					</div>					
					<div style="margin-right:-300px; width: 280px; float:right;">
						<div id="submitdiv">
							<h3 class="hndle"><span><?php _e( 'Enregistrer', 'tify' );?></span></h3>
							<div style="padding:10px;">
								<div class="submit">
								<?php submit_button(); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	<?php
	}
}
global $tify_options;
$tify_options = new tiFy_Options;

/* = HELPER = */
/** == Definition des options à enregistrer depuis la page == **/
function tify_options_register_settings( $settings ){
	global $tify_options;
	
	foreach( (array) $settings as $setting ) 
		array_push( $tify_options->register_settings, $setting );
}

/** == Definition d'un boîte de saisie == **/ 
function tify_options_register_node( $node ){
	global $tify_options;
	
	array_push( $tify_options->nodes, $node );
}