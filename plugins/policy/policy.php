<?php
/*
Plugin Name: Policy
Plugin URI: http://presstify.com/policy
Description: Gestion de la politique de site
Version: 1.141127
Author: Milkcreation
Author URI: http://milkcreation.fr
Text Domain: tify_policy
*/

class tiFy_policy{
	var $tiFy;
	
	/**
	 * Initialisation
	 */
	function __construct( ){
		global $tiFy;
		
		$this->tiFy = $tiFy;
	}	
}
new tiFy_policy;