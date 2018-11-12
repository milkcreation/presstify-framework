<?php
class tiFy_Tabooxes_Post extends tiFy_Tabooxes{
	/* = ARGUMENTS = */
	public 	// Configuration
			$type = 'post',
			$node_capability = 'edit_posts';
		
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Tabooxes_Master $master ){
		parent::__construct( $master );
		
		// Actions et Filtres Wordpress
		add_action( 'add_meta_boxes', array( $this, 'wp_add_meta_boxes' ) );		
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Déclaration de la metaboxe de saisie des métadonnées == **/
	function wp_add_meta_boxes( $post_type ){
		// Bypass
		if( ! isset( $this->nodes[$post_type] ) )
			return;

		if( $post_type == 'page' ) 
			add_action( 'edit_page_form', array( $this, 'box_render' ) );
		else 
			add_action( 'edit_form_advanced', array( $this, 'box_render' ) );					
	}
	
	/* = CONTROLEURS = */
	/** == Déclaration d'une boîte à onglets == **/
	function register_box( $hookname, $args = array()  ){
		if( is_string( $hookname ) )
			$post_types = array( $hookname );
		else 
			$post_types = $hookname;		

		foreach( (array) $post_types as $post_type ) 
			if( in_array( $post_type, get_post_types() ) )
				$this->set_box( $post_type, $args );	
	}
	
	/** == Déclaration d'une section de boîte à onglet == **/
	function register_node( $hookname, $args = array()  ){
		if( is_string( $hookname ) )
			$post_types = array( $hookname );
		else
			$post_types = $hookname;
			
		foreach( (array) $post_types as $post_type ) :
			$this->add_node( $post_type, $args );			
		endforeach;
	}
	
	/** == Ajout d'une section de boîte à onglet == **/
	function add_node( $screen, $node ){
		$node = $this->parse_node( $node );
		
		if( !isset( $this->boxes[ $screen ] ) )
			$this->set_box( $screen );
		
		$this->nodes[ $screen ][ $node['id'] ] = array_merge( $node, array( 'screen_type' => $this->type, 'screen_page' => $screen ) );
	}	
}

/* = ALIAS = */
/** == Déclaration d'une boîte à onglets de post == **/
function tify_taboox_register_box_post( $hookname, $box_args = array() ){
	tify_taboox_register_box( $hookname, $box_args, 'post' );
}

/** == Déclaration d'une section de boîte à onglets de post == **/
function tify_taboox_register_node_post( $hookname, $node_args ){
	tify_taboox_register_node( $hookname, $node_args, 'post' );	
}