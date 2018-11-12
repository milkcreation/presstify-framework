<?php
class tiFy_tabooxes_user extends tiFy_Tabooxes{
	/* = ARGUMENTS = */
	public $type = 'user';
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_tabooxes_master $master ){
		parent::__construct( $master );
		
		// Actions et Filtres Wordpress
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation de l'administration == **/
	function admin_init(){
		$this->set_box( 
			'user-edit',
			array( 
				'title'		=> __( 'Informations Complémentaires', 'tify' )
			)
		);
		add_action( 'edit_user_profile', array( $this, 'box_render' ) );
		
		$this->set_box(
			'profile',
			array( 
				'title'		=> __( 'Informations Complémentaires', 'tify' )
			)
		);
		
	}
}

/* = HELPER = */
/** == Ajout d'un onglet == **/
function tify_taboox_user_add_node( $screen, $node ){
	global $tify_tabooxes_master;

	$tify_tabooxes_master->user->add_node( $screen, $node );
}