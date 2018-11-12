<?php
class tiFy_Tabooxes_Option extends tiFy_Tabooxes{
	/* = ARGUMENTS = */
	public 	// Configuration
			$type = 'option';
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Tabooxes_Master $master ){
		parent::__construct( $master );
		
		// Actions et Filtres Wordpress
		add_action( 'admin_init', array( $this, 'wp_admin_init' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation de l'administration == **/
	function wp_admin_init(){
		foreach( $this->boxes as $hookname => $args ) :
			add_settings_section( $hookname, null, array( $this, 'box_render' ), $args['page'] );
		endforeach;
	}
	
	/* = CONTROLEUR = */
	/** == Déclaration d'une boîte à onglets == **/
	function register_box( $hookname, $args = array()  ){
		$this->set_box( $hookname, $args );
		$this->page[$hookname] = $args['page'];
	}
	
	/** == Ajout d'une section de boîte à onglet == **/
	function add_node( $screen, $node ){
		$node = $this->parse_node( $node );
		$this->nodes[ $screen ][ $node['id'] ] = array_merge( $node, array( 'screen_type' => $this->type, 'screen_page' => $this->page[ $screen ] ) );
	}
		
	/** == Rendu de l'interface d'administration == **/
	function box_render( ){		
		// Récupération des arguments
		$args = array();

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
}

/* = ALIAS = */
/** == Déclaration d'une boîte à onglets d'option == **/
function tify_taboox_register_box_option( $hookname, $box_args = array() ){
	tify_taboox_register_box( $hookname, $box_args, 'option' );
}

/** == Déclaration d'une section de boîte à onglets d'option == **/
function tify_taboox_register_node_option( $hookname, $node_args ){
	tify_taboox_register_node( $hookname, $node_args, 'option' );	
}