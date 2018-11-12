<?php
class tiFy_Tabooxes_Taxonomy extends tiFy_Tabooxes{
	/* = ARGUMENTS = */
	public $type = 'taxonomy';
		
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Tabooxes_Master $master ){
		parent::__construct( $master );
		
		// Initialisation des prérequis
		/*tify_require( 'taxonomy_metadata' );
		new Taxonomy_Metadata;*/
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Enregistrement des métadonnées de taxonomie == **/
	function wp_edited_taxonomy( $term_id, $tt_id ){
		// Contrôle s'il s'agit d'une routine de sauvegarde automatique.	
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	      return;
		// Contrôle s'il s'agit d'une routine de sauvegarde automatique.	
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
	      return;
		// Récupération de la taxonomie  
		preg_match( '/^edited_(.*)/', current_filter(), $taxnow );	
		if( ! isset( $taxnow[1] ) )
			return;
		if( ! $tax = get_taxonomy( $taxnow[1] ) )
			return;
		// Contrôle des permissions sur la taxonomie
		if ( ! current_user_can( $tax->cap->manage_terms ) )
	        return;
				 
		$metas = ( isset( $_POST['taxonomy_box'] ) ) ? $_POST['taxonomy_box'] : null;
		
		$metas = apply_filters( 'taxonomy_box_sanitize_metadata', $metas, $term_id );
			
		foreach( (array) $metas as $metakey => $metavalue ) :
			if ( empty( $metavalue ) ):
				delete_term_meta( $term_id, '_'. $metakey );
			elseif ( $metavalue != get_term_meta( $term_id, '_'. $metakey, true ) ):
				update_term_meta( $term_id, '_'. $metakey, $metavalue );		
			endif;		
		endforeach;
	
		return $term_id;
	}	
	
	/* = CONTROLEUR = */
	/** == Déclaration d'une boîte à onglets == **/
	function register_box( $hookname, $args = array()  ){
		if( is_string( $hookname ) )
			$taxonomy = array( $taxonomy );
	
		foreach( ( array ) $taxonomy as $tax ) :	
			$this->set_box( 
				'edit-'. $tax,
				array( 
					'title'		=> isset( $args['title'] ) ? $args['title'] : '',
					'scripts'	=> isset( $args['scripts'] ) ? $args['scripts'] : '',
				)
			);
			
			add_action( $tax .'_edit_form', array( $this, 'box_render' ), null, 2);
			add_action( 'edited_'. $tax, array( $this, 'wp_edited_taxonomy' ), null, 2 );
		endforeach;	
	}
	
	/** == Déclaration d'une section de boîte à onglet == **/
	function register_node( $hookname, $args = array()  ){
		if( is_string( $hookname ) )
			$taxonomy = array( $taxonomy );
	
		foreach( ( array ) $taxonomy as $tax ) 
			$this->add_node( 'edit-'. $tax, $node );
	}	
}

/* = ALIAS = */
/** == Déclaration d'une boîte à onglets de taxonomy == **/
function tify_taboox_register_box_taxonomy( $hookname, $args = array() ){
	tify_taboox_register_box( $hookname, $args, 'taxonomy' );
}

/** == Déclaration d'une section de boîte à onglets de taxonomy == **/
function tify_taboox_register_node_taxonomy( $hookname, $node ){
	tify_taboox_register_node( $hookname, $args, 'taxonomy' );	
}