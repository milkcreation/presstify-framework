<?php
/**
 * @Overridable 
 */
namespace tiFy\Forms\Fields\Html;

use tiFy\Forms\Fields\Factory;

class Html extends Factory
{
	/* = ARGUMENTS = */
	// Identifiant
	public $ID 	       = 'html';
	
	// Support
	public $Supports   = array( 'wrapper' );
	
	
	/* = CONTRÔLEUR = */
	/** == Affichage == **/
	public function display()
	{
		return $this->field()->getAttr( 'value' ); 
	}
}