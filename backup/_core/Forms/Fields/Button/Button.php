<?php

/**
 * @Overridable 
 */

namespace tiFy\Forms\Fields\Button;

class Button extends \tiFy\Forms\Fields\Factory
{
	/* = ARGUMENTS = */
	// Identifiant
	public $ID = 'button';
	
	// Support
	public $Supports = array(
		'wrapper'
	);	
		
	/* = CONTROLEURS = */
	/** == Affichage == **/
	public function display()
	{				
		if( ! $className = \tiFy\Forms\Buttons::getClass( $this->field()->getAttr( 'value' ) ) )
			return;
		
		$button = new $className;
		$button->init( $this->form(), $this->field()->getOptions() );
		
		return $button->display();
	}
}