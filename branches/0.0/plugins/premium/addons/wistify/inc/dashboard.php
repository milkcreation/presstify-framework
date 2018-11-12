<?php 
class tiFy_wistify_dashboard{
	
	function __construct( tiFy_Wistify_Master $master ){
		$this->master = $master;
		
		// ACTIONS ET FILTRES WORDPRESS
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Menu d'administration == **/
	function wp_admin_menu(){
		add_submenu_page( 'tify_wistify', __( 'Tableau de bord', 'tify' ), __( 'Tableau de bord', 'tify' ), 'manage_options', 'tify_wistify', array( $this, 'view_admin_render' ) );
	}
	
	/* = VUES = */
	function view_admin_render(){
	
	}
}