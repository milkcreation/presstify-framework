<?php
/**
USAGE :
 
Déclaration d'une boîte à onglets
----------------------------------
add_action( 'tify_taboox_register_box', [function_register_box] );  
function function_register_box( ){
	tify_taboox_register_box(
 		[hookname],						// (string) get_current_screen()->id
 		array(
 			'title'		=> ''			// (optionnel) Titre de la boîte à onglet			
 			
			// @deprecated 
			'scripts	=>	array()		// hookname des scripts JS		
 		),
		[type]							// (string) option | post | taxonomy | user
 	);
}
alias :
tify_taboox_register_box_option, tify_taboox_register_box_post, tify_taboox_register_box_taxonomy, tify_taboox_register_box_user
 

Déclaration d'un section de boîte à onglets
-------------------------------------------
add_action( 'tify_taboox_register_node', [function_register_node] );  
function function_register_node( ){
	tify_taboox_register_node(
 		[hookname],									// (string) get_current_screen()->id
		array( 
	 		'id' 			=> false,				// (string) requis
			'title' 		=> '',					// (string)	requis
			'order'			=> 99					// Ordre d'affichage de la section
			
			'parent'		=> false,				// id de la section parente	
			'cb' 			=> __return_null(),		// Fonction de callback
			'args' 			=> array(),				// 

			//@todo
			'capability'	=> 'manage_options',	// Habilitation d'accès à la section
 			'nested'		=> array()				// Callback des interfaces de saisie incluses 

			//@deprecated		
			'scripts'		=> array(),				// hookname des scripts JS
			'styles'		=> array(),				// hookname des feuilles de styles CSS
			
 		),
		[type]										// (string) option | post | taxonomy | user 
 	)
}
alias :
tify_taboox_register_node_option, tify_taboox_register_node_post, tify_taboox_register_node_taxonomy, tify_taboox_register_node_user
 

Déclaration d'une interface de saisie personnalisée
---------------------------------------------------
add_action( 'tify_taboox_register_form', '[function_register_form]' );
function function_register_form(){
	tify_taboox_register_form( [taboox_RegisterFormClass], $passed_args );
}

Class taboox_RegisterFormClass extends tiFy_Taboox{
	// (requis) Constructeur
	function __construct( $passed_args ){	
		parent::__construct(
			// Options
			array(
				'environments'		=> array( 'option' ),		// Environnements valides
				'dir'				=> dirname( __FILE__ ),		// Répertoire
				'instances'  		=> 1						// Nombre d'instance possible
			)
		);
	}
 	
	// (requis) Formulaire de saisie
	public function form(){}

	// (optionel) Déclaration des scripts JS et CSS requis par le formulaire
	public function register_scripts(){ }
	
	// (optionel) Mise en file des scripts JS et CSS
	public function enqueue_scripts(){ }
	
	// (optionel post uniquement) Action lancée lors de la sauvegarde des posts
	public function save_post( $post_id, $post ){
		return $post_id;
	}
	
	// (optionel option uniquement) Action lancée lors de la sauvegarde des options
	public function sanitize_option( $value, $option ){
		return $value;
	}
	
	// (optionel option uniquement) Action lancée lors de la sauvegarde des options pour le [name] déclaré
	public function sanitize_option_[option_name]( $value, $option ){
		return $value;
	}
} 
   
 */


/* = HELPER = */
global $tify_tabooxes_master;

/** == Déclaration d'une boîte à onglets == **/
function tify_taboox_register_box( $hookname = '', $box_args = array(), $type = null ){
	global $tify_tabooxes_master;
	
	// Bypass
	if( ! $hookname || ! $type )
		return;
	
	return $tify_tabooxes_master->{$type}->register_box( $hookname, $box_args );
}

/** == Déclaration d'une section de boîte à onglets == **/
function tify_taboox_register_node( $hookname = '', $node_args = array(), $type = null ){
	global $tify_tabooxes_master;
	
	if( ! $hookname || ! $type )
		return;
	
	$tify_tabooxes_master->{$type}->register_node( $hookname, $node_args );
}

/** == Déclaration d'une interface de saisie personnalisée == **/
function tify_taboox_register_form( $form_class, $passed_args = array() ){
	global $tify_tabooxes_master;
	
	$tify_tabooxes_master->register( $form_class, $passed_args );
} 

/* = CLASSE PRINCIPALE = */
class tiFy_Tabooxes_Master{
	/* = ARGUMENTS = */
	public	// Chemins					
			$dir,
			$uri,
			// Configuration
						
			// Contrôleurs
			$option,
			$post,
			$taxonomy,
			$user,
						
			// Paramètres
			$boxes,
			$tabooxes; 
	
	/* = CONSTRUCTEUR = */
	function __construct( ){
		// Définition des chemins
		$this->dir = dirname( __FILE__ );
		$this->uri = plugin_dir_url( __FILE__ );
		
		// Controleurs principaux
		require_once $this->dir .'/inc/tabooxes-option.php';
		$this->option 	= new tiFy_tabooxes_option( $this );
		require_once $this->dir .'/inc/tabooxes-post.php';
		$this->post 	= new tiFy_tabooxes_post( $this );
		require_once $this->dir .'/inc/tabooxes-taxonomy.php';
		$this->taxonomy = new tiFy_tabooxes_taxonomy( $this );
		require_once $this->dir .'/inc/tabooxes-user.php';
		$this->user 	= new tiFy_tabooxes_user( $this );
		
		// Interfaces de saisie prédéfinies (taboox)
		require_once $this->dir .'/taboox/color_palette/color_palette.php';
		require_once $this->dir .'/taboox/custom_background/custom_background.php';
		require_once $this->dir .'/taboox/custom_fields/custom_fields.php';
		require_once $this->dir .'/taboox/custom_header/custom_header.php';
		require_once $this->dir .'/taboox/date_range/date_range.php';
		require_once $this->dir .'/taboox/date_single/date_single.php';
		require_once $this->dir .'/taboox/dynamic_tabs/dynamic_tabs.php';
		require_once $this->dir .'/taboox/fileshare/fileshare.php';
		require_once $this->dir .'/taboox/google_map/google_map.php';	
		require_once $this->dir .'/taboox/images_gallery/images_gallery.php';
		require_once $this->dir .'/taboox/links/links.php';
		require_once $this->dir .'/taboox/password_protect/password_protect.php';
		require_once $this->dir .'/taboox/related_posts/related_posts.php';	
		require_once $this->dir .'/taboox/simple_video/simple_video.php';
		//require_once $this->dir .'/taboox/slideshow/slideshow.php';
		//require_once $this->dir .'/taboox/simple_google-map/simple_google-map.php';
		require_once $this->dir .'/taboox/taxonomy_select/taxonomy_select.php';
		require_once $this->dir .'/taboox/videos_gallery/videos_gallery.php';
		
		// Compatibilité
		require_once $this->dir .'/inc/deprecated.php';
			
		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ), 1 );
		add_action( 'admin_init', array( $this, 'wp_admin_init' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation globale == **/
	function wp_init(){
		do_action( 'tify_taboox_register_form' );
	}
	
	/** == Initialisation de l'administration == **/	
	function wp_admin_init(){	
		// Chargement des scripts des interfaces de saisi prédéfinies
		foreach( glob( $this->dir .'/taboox/*' ) as $dir ) :
			$basename 	= basename( $dir );
			$css_path 	= untrailingslashit( $dir ).'/admin.css';
			$js_path	= untrailingslashit( $dir ).'/admin.js';
			if( file_exists( $css_path ) ) :
				$script_data =  $this->get_script_data( $css_path );
				wp_register_style( 'taboox-'. $script_data['hookname'], $this->uri .'/taboox/'. $basename .'/admin.css', $script_data['dependencies'], $script_data['version'] );
			endif;
			if( file_exists( $js_path ) ) :
				$script_data =  $this->get_script_data( $js_path );
				wp_register_script( 'taboox-'. $script_data['hookname'], $this->uri .'/taboox/'. $basename .'/admin.js', $script_data['dependencies'], $script_data['version'], true );	
			endif;
		endforeach;
	}
	
	/* = CONTROLEURS = */
	/** == Déclaration des tabooxes == **/
	function register( $taboox_class, $args = array() ) {
		$this->tabooxes[$taboox_class] = new $taboox_class( $args );
	}
	
	/** == Récupération des données de script == */
	function get_script_data( $path ){
		$default_headers = array(
			'hookname' 		=> 'Hookname',
			'dependencies'  => 'Dependencies',
			'version'		=> 'Version'
		);
		
		$script_data = get_file_data( $path, $default_headers );
		
		$default_datas = array( 
			'hookname' 		=> basename( dirname( $path ) ),
			'dependencies'	=> '',
			'version'		=> '1.1'
		);
	
		foreach(  $script_data as $field => &$v ) :
			if( ! $v )  $v = $default_datas[$field];
			if( $field == 'hookname' )
				$v = wp_unique_filename( $v, $v );
			if( $field == 'dependencies' && !empty( $v ) )
				$v = array_map( 'trim', explode( ',', $v ) );
		endforeach;
	
		return $script_data;
	}	
}
$tify_tabooxes_master = new tiFy_tabooxes_master;

/* = CLASSE DES BOITES A ONGLETS ET SECTIONS  = */
class tiFy_Tabooxes{
	/* = ARGUMENTS = */
	public  // Chemins
			$dir,
			$uri,
			
			// Configuration
			$type,
			$node_capability = 'manage_options',
			
			// Paramètres
			$screens	= array(),
			$screen,
			$nodes 		= array(),
			$boxes 		= array(),
			$page 		= array(),
			$editboxes 	= array(),
			$current_group,
			
			// Référence
			$master;
		
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Tabooxes_Master $master ){
		// Instanciation de la classe de référence		
		$this->master 	= $master;
		
		// Définition des chemins
		$this->dir 		= $this->master->dir;
		$this->uri  	= $this->master->uri;
		
		// Actions et Filtres Wordpress
		add_action( 'admin_init', array( $this, '_wp_admin_init' ) );
		add_action( 'current_screen', array( $this, '_wp_current_screen' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, '_wp_admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_editbox_current_tab', array( $this, '_wp_ajax' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation de l'administration == **/
	function _wp_admin_init(){
		do_action( 'tify_taboox_register_box' );
		do_action( 'tify_taboox_register_node' );		
			
		// Déclaration des scripts 	
		wp_register_style( 'tify_tabooxes', $this->uri .'/tify_tabooxes.css', array(), '150216'  );
		wp_register_script( 'tify_tabooxes', $this->uri .'/tify_tabooxes.js', array( 'jquery' ), '150216', true );
	}
	
	/** == == **/
	function _wp_current_screen(){
		foreach( (array) $this->nodes as $get_current_screen_id ) :
			foreach( (array) $get_current_screen_id as $id => $args ) :
				if( is_string( $args['cb'] ) && class_exists( $args['cb'] ) && isset( $this->master->tabooxes[ $args['cb'] ] ) ) :
					$this->master->tabooxes[ $args['cb'] ]->set_screen( $args['screen_type'], $args['screen_page'] );
				endif;
			endforeach;
		endforeach;
	}
	 
	/** == Mise en file des scripts de l'interface d'administration == **/
	function _wp_admin_enqueue_scripts(){
		// Bypass
		if( ! is_array( $this->nodes ) )
			return;
		if( ! in_array( get_current_screen()->id, $this->screens ) )
			return;

		wp_enqueue_style( 'tify_tabooxes' );
		wp_enqueue_script( 'tify_tabooxes' );
		
		//Bypass
		if( ! isset( $this->boxes[get_current_screen()->id] ) )
			return;

		if( is_array( $this->boxes[get_current_screen()->id]['scripts'] ) ) :	
			foreach( (array) $this->boxes[get_current_screen()->id]['scripts'] as $script ) : 
				$handle = 'taboox-'.$script;
				if( wp_style_is( $handle, 'registered' ) ) :
					wp_enqueue_style( $handle );
				endif;
				if( wp_script_is( $handle, 'registered' ) ) :
					wp_enqueue_script( $handle );
				endif;
			endforeach;
		endif;
		
		if( ! empty( $this->nodes[get_current_screen()->id] ) ) :
			foreach( (array) $this->nodes[get_current_screen()->id] as $id => $args ) :
				if( is_string( $args['cb'] ) && class_exists( $args['cb'] ) && isset( $this->master->tabooxes[ $args['cb'] ] ) ) 
					$this->master->tabooxes[ $args['cb'] ]->enqueue_scripts( );
				foreach( (array) $args['styles'] as $handle ) :
					
					if( wp_style_is( $handle, 'registered' ) )
						wp_enqueue_style( $handle );
				endforeach;
				foreach( (array) $args['styles'] as $handle ) 
					if( wp_script_is( $handle, 'registered' ) )
						wp_enqueue_script( $handle );
			endforeach;
		endif;
	}

	/** == Sauvegarde de la tabulation courante == **/
	function _wp_ajax(){
		if( empty( $_POST['tabindex'] ) )
			wp_die(0);
		update_user_meta( get_current_user_id(), $_POST['tabindex'], ! empty( $_POST['current'] ) ? $_POST['current']:0 );
		wp_die( 1 );
	}
	
	/* = CONTROLEURS = */
	/** == Déclaration == **/
	function register_box( $hookname, $args = array() ){
		$this->set_box( $hookname, $args );
	}
	
	/** == Définition de la boîte à onglet == **/
	function set_box( $screen, $args = array() ){
		$defaults = array(
			'title' 	=> '',
			'scripts'	=> array()
		);
		$this->screens[] = $screen;
		$this->boxes[$screen] = wp_parse_args( $args, $defaults );
		$this->master->boxes[$screen] = &$this;
	}
	
	/** == Déclaration d'une section de boîte à onglet == **/
	function register_node( $hookname, $args = array()  ){
		$this->add_node( $hookname, $args );
	}	
		
	/** == Ajout d'une section de boîte à onglet == **/
	function add_node( $screen, $node ){
		$node = $this->parse_node( $node );
		$this->nodes[ $screen ][ $node['id'] ] = $node;
	}
	
	/** == Traitement d'une section de boîte à onglet == **/
	function parse_node( $node ){
		$defaults = array(
			'id' 			=> false,
			'title' 		=> '',
			'cb' 			=> __return_null(),
			'parent'		=> 0,
			'args' 			=> array(),
			'scripts'		=> array(),
			'styles'		=> array(),
			'capability'	=> $this->node_capability,
			'order'			=> 99
		);
		return wp_parse_args( $node, $defaults );
	}
	
	/** == Récupération d'une section de boîte à onglet == **/
	function get_node( $screen, $id ){
		if( isset( $this->nodes[ $screen ][ $id ] ) )
			return $this->nodes[ $screen ][ $id ];
	}
	
	/** == == **/
	function parse_editbox( $screen ){
		// Bypass
		if( ! isset( $this->nodes[ $screen ] ) )
			return array();
		$nodes = $this->nodes[ $screen ];
				
		// Découverte des parents
		$parent_nodes = array(); $order = array();
		foreach( $nodes as $id => $node ) :
			if( ! $node['parent'] ) :
				$parent_nodes[] = $id;
				$order[ $id ] = $node['order'];
				$this->editboxes[ $screen ][ $id ] = array();
				unset( $nodes[ $id ] );
			endif;
		endforeach;
		// Trie des parents
		@array_multisort( $order, $this->editboxes[ $screen ] );
				
		// Découverte des enfants
		$child_nodes = array(); $order = array();
		foreach( $nodes as $id => $node ) :
			if( in_array( $node['parent'], $parent_nodes ) ) :
				$child_nodes[ $id ] = $node['parent'];
				$order[ $node['parent'] ][ $id ] = $node['order'];
				$this->editboxes[ $screen ][ $node['parent'] ][ $id ] = array();
				unset( $nodes[ $id ] );
			endif;
		endforeach;
		// Trie des enfants
		foreach( $order as $node_parent => $ord )
			@array_multisort( $ord, $this->editboxes[ $screen ][$node_parent] );
		
		// Découverte des petits-enfants
		$grandchild_nodes = array();
		foreach( $nodes as $id => $node ) :
			if( isset( $child_nodes[ $node['parent'] ] ) ) :
				$grandchild_nodes[ $id ] = $node['parent'];
				$this->editboxes[ $screen ][ $child_nodes[ $node['parent'] ] ][ $node['parent'] ][$id] = array();
				unset( $nodes[ $id ] );
			endif;
		endforeach;
	}
	
	/** == Rendu de l'interface d'administration == **/
	function box_render( ){	
		// Récupération des arguments
		$args = ( func_num_args() ) ? func_get_args() : array();

		// Définition de l'écran actif
		$screen = get_current_screen()->id;		
		$this->parse_editbox( $screen );	
	?>
		<?php foreach( (array) $this->editboxes as $editbox => $nodes ) : ?>
			<div id="taboox-container-<?php echo $editbox;?>" class="taboox-container">
				<h3 class="hndle"><span><?php echo ! empty( $this->boxes[$screen]['title'] ) ? $this->boxes[$screen]['title'] : __( 'Réglages généraux', 'tify' ); ?></span></h3>
				<?php array_unshift( $args, array( 'nodes' => $nodes, 'depth' => 1 ) ); ?>
				<?php $this->nodes_render( $args );?>
			</div>
		<?php endforeach;?>
	<?php
	}
	
	/**
	 * Rendu de la boîte à onglets
	 */
	function nodes_render( $args = array() ){
	?>
		<div class="wrapper">					
			<div class="back"></div>					
			<div class="wrap">
				<div class="tabbable tabs-left">							
					<?php $this->nav_tab_render( $args );?>	
					<?php $this->tab_content_render( $args );?>						
				</div>
			</div>
		</div>
	<?php
	}	
	
	/**
	 * Onglets de navigation
	 */
	function nav_tab_render( $args = array() ){
		if( $args[0]['depth'] == 2 )
			$class = 'nav nav-tabs';
		elseif( $args[0]['depth'] == 3 )
			$class = 'nav nav-pills';
		else
			$class = 'nav nav-tabs';			
	?>	
		<ul class="<?php echo $class;?>">
			<?php foreach( $args[0]['nodes'] as $id => $child_nodes ) : $data = $this->get_node( get_current_screen()->id, $id );?>					
			<li class="<?php if( $this->in_current_group( $id ) ) echo 'active';?>">
				<a data-toggle="tab" data-current="<?php echo 'node-'.$id;?>" data-group="<?php echo get_current_screen()->id;?>" href="#<?php echo 'node-'.$id;?>">
					<?php echo $data['title'];?>
				</a>
			</li>
			<?php endforeach;?>				
		</ul>
	<?php
	}
	
	/**
	 * Page de contenu des onglets de navigation
	 */
	function tab_content_render( $args ){		
		// Informations sur le noeud courant
		$depth = $args[0]['depth'];
		$nodes = $args[0]['nodes'];
		$node_args = array();
	?>
		<div class="tab-content">
		<?php foreach( $nodes as $id => $child_nodes ) : ?>
			<?php $data = $this->get_node( get_current_screen()->id, $id ); ?>
			<div id="<?php echo 'node-'. $id;?>" class="tab-pane <?php if( $this->in_current_group( $id ) ) echo 'active';?>">
				<?php if( ! empty( $child_nodes ) ) : ?>
					<?php $args[0] = array( 'depth' => $depth+1, 'nodes' => $child_nodes ); // Affectation des infos du noeud avec les valeur du noeud enfant ?>
					<div class="tabbable tabs-top">							
						<?php $this->nav_tab_render( $args );?>		
						<?php $this->tab_content_render( $args );?>						
					</div>
					<?php $args[0] = array( 'depth' => $depth, 'nodes' => $nodes ); // Rétablissement des infos originales du noeud ?>
				<?php elseif( ! empty( $data['cb'] ) ) :  ?>
					<?php array_shift( $args ); // Suppression des infos du noeud ?>					
					<?php array_push( $args, $data['args'], $this->type  ); // Ajout des arguments supplémentaires ?>
					<div class="tab-content">
						<div class="node-content">
							<?php if( ! current_user_can( $data['capability'], $data['id'] ) ) :?>
								<h3 class="current_user_cannot"><?php _e( 'Vous n\'êtes pas habilité à administrer cette section', 'tify' );?></h3>
							<?php else : ?>
							<?php 
								if( isset( $data['cb'] ) && is_string( $data['cb'] ) && class_exists( $data['cb'] ) && isset( $this->master->tabooxes[ $data['cb'] ] ) ) : 
									$this->master->tabooxes[ $data['cb'] ]->_form( $args );				
								else :
									call_user_func_array( $data['cb'], $args ); 
								endif;
							?>
							<?php endif;?>
						</div>
					</div>
					<?php array_pop( $args ); // Suppression de l'information sur le type ?>
					<?php array_pop( $args ); // Suppression des arguments de noeud ?>
					<?php array_unshift( $args, array( 'depth' => $depth, 'nodes' => $nodes ) ); // Réinjection des infos originales du noeud ?>
				<?php endif;?>
			</div>
		<?php endforeach;?>
		</div>
	<?php	
	}
	
	/**
	 * 
	 */
	function get_current_group(){
		if( $this->current_group )
			return $this->current_group;
		// Récupération du group d'onglet courant
		$current_groups = array();
		if( $current =  get_user_meta( get_current_user_id(), get_current_screen()->id, true ) )
			$this->current_group = explode( ',', $current );	
		else
			$this->current_group = array( get_current_screen()->id );
		
		return $this->current_group;
	}
	
	/**
	 * 
	 */
	function in_current_group( $node ){
		return in_array( 'node-'. $node, $this->get_current_group() );
	}
}

/* = CLASSE DES ZONES DE SAISIE = */
class tiFy_Taboox{
	/* = ARGUMENTS = */
	protected 	// Options
				$type,
				$page,
				$environments,
				$instances,
				
				/// Chemins
				$dir,		// Chemins absolu vers le repertoire
				$path,		// Chemins relatif vers le repertoire
				$uri;		// Url absolue vers le repertoire		 
	
	// Options de la boîte courante
	public		$env,			// Environnement ( option | post | taxonomy | user )
				$instance = 0,  // Instance courante	
										 
				$post, 
				$term, 
				$taxonomy,
				$profile_user,
				
				$args, 
				$name,
				$names = array(),
				$value,
				$defaults;
	
	/* = CONSTRUCTEUR = */
	public function __construct( $options ){		
		// Définition des options		
		$valid_options = array( 'dir', 'environments', 'instances' );
		foreach( $options as $kopt => $vopt )
			if( in_array( $kopt, $valid_options ) )
				$this->{$kopt} = $vopt;
		
		// Définition des chemins
		if( ! $this->dir )
			$this->dir 	= dirname( __FILE__ );		
		$this->path = tify_get_relative_path( $this->dir );
		$this->uri 	= site_url( '/'. $this->path );
		
		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, '_wp_init' ) );
		add_action( 'admin_init', array( $this, '_wp_admin_init' ), 99 );
		add_action( 'save_post', array( $this, '_wp_save_post' ), null, 2 );
		add_filter( 'sanitize_option'. $this->name, array( $this, '_wp_sanitize_option' ), null, 2 );
	}
	
	public function set_screen( $type, $page ){		
		$this->type = $type; $this->page = $page;
		if( $this->type === 'option' && $this->name ) :	
			register_setting( $this->page, $this->name );
		endif;
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation global == **/
	public function _wp_init(){}
	
	/** == Initialisation de l'administration == */
	public function _wp_admin_init(){
		$this->register_scripts();
	}
	
	/** == Initialisation global == **/
	public function _wp_save_post( $post_id, $post ){
		// Contrôle s'il s'agit d'une routine de sauvegarde automatique.	
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	      return;
		// Contrôle s'il s'agit d'une routine de sauvegarde automatique.	
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
	      return;	
		
		//Bypass
		if( ! isset( $_POST['post_type'] ) )
			return;
			
		// Contrôle des permissions
	  	if ( 'page' == $_POST['post_type'] )
	    	if ( ! current_user_can( 'edit_page', $post_id ) )
	        	return;
	  	else
	    	if ( ! current_user_can( 'edit_post', $post_id ) )
	        	return;
				
		if( ( ! $post = get_post( $post_id ) ) || ( ! $post = get_post( $post ) ) )
			return;	
		
		$this->save_post( $post_id, $post );
	}
	
	/** == == **/
	function _wp_sanitize_option( $value, $option ){
		if( ! $this->name )
			return $value;
		if( $this->type !== 'option' )
			return $value;
		
		if( is_callable( array( $this, 'sanitize_option_'. $option ) ) )
			call_user_func( array( $this, 'sanitize_option_'. $option ), $value, $option );
		else
			$this->sanitize_option( $value, $option );
	}
	
		
	/* = METHODES PUBLIQUES = */
	/** == Déclaration des scripts == **/
	public function register_scripts(){}
	
	/** == Mise en file des scripts == **/
	public function enqueue_scripts(){}
	
	/** == Formulaire de saisie == **/
	public function form(){}
	
	/** == Action lancée lors de la sauvegarde des posts == **/
	public function save_post( $post_id, $post ){
		return $post_id;
	}
	
	/** == Action lancée lors de la sauvegarde des options == **/
	public function sanitize_option( $value, $option ){
		return $value;
	}
	
	/* = METHODES PRIVÉES = */
	/** == Traitement et retour du formulaire de saisie == **/
	public function _form( $args = array() ){
		$this->_parse_args( $args );
		return $this->form();
	}
	
	/** == Traitement des arguments == **/
	protected function _parse_args( $args ){
		$this->instance++;

		$this->env = array_pop( $args );
		$this->args = array_pop( $args );

		switch( $this->env ) :
			case 'option' :
				$this->name 	= ! isset( $this->args['name'] ) ? $this->name : $this->args['name']; 				
				$this->value 	= get_option( $this->name, $this->defaults );
				break;
			case 'post' :
				$this->post = array_pop( $args );
				// Bypass
				if( ! $post = get_post( $this->post ) )
					return;
				$this->name 	= ! isset( $this->args['name'] ) ? $this->name : $this->args['name'];					
				$this->value 	= get_post_meta( $post->ID, '_'. $this->name, true );
				$this->name 	= "mkpbx_postbox[single][{$this->name}]";
				break;
			case 'taxonomy' :
				$this->taxonomy = array_pop( $args );
				$this->term 	= array_pop( $args );
				
				$this->name 	= ! isset( $this->args['name'] ) ? $this->name : $this->args['name']; 
				$this->value	= get_term_meta( $this->term->term_id, '_'. $this->name, true );
				$this->name 	= "taxonomy_box[{$this->name}]";
				break;
			case 'user' :
				$this->profile_user = array_pop( $args );
				break;
		endswitch;
	}
}