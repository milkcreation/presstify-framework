<?php
/*
Plugin Name: Admin Manager
Plugin URI: http://presstify.com/admin-manager
Description: Personnalisation de l'interface d'administration
Version: 1.141127
Author: Milkcreation
Author URI: http://milkcreation.fr
Text Domain: tify_admin_manager
*/

class tiFy_admin_manager{
	var $tiFy;
	
	/**
	 * Initialisation
	 */
	function __construct( ){
		global $tiFy;
		
		$this->tiFy = $tiFy;
	}	
}
new tiFy_admin_manager( );