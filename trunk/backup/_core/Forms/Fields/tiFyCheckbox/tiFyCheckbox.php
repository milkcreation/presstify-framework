<?php
/**
 * @Overridable 
 */

namespace tiFy\Forms\Fields\tiFyCheckbox;

use tiFy\Control\Control;

class tiFyCheckbox extends \tiFy\Forms\Fields\Factory
{
	/* = ARGUMENTS = */
	// Identifiant
	public $ID 			= 'tify_checkbox';
	
	// Support
	public $Supports 	= array(
		'integrity',
		'label', 
		'request',
		'wrapper'
	);
	
	// Instance
	private static $Instance;
		
	/* = CALLBACKS = */	
	/** == Affichage du champ == **/
	public function display()
	{		
		if( ! self::$Instance ) :
            Control::enqueue_scripts('Checkbox');
		endif;
		
		self::$Instance++;
			
		$selected = $this->field()->getValue();		
		$output = "";
		foreach( (array) $this->field()->getAttr( 'choices' ) as $value => $label ) :
			$checked = ( is_array( $selected ) ) ? in_array( $value, $selected ) : $selected;
			$output .= Control::Checkbox(
				array(
					'id'				=> $this->getInputID(),
					'class'				=> join( ' ', $this->getInputClasses() ),
					'name'				=> $this->field()->getDisplayName(),
					'checked'			=> $checked,
					'value'				=> $value,
					'label'				=> $label,
					'label_class'		=> 'choice-title'
				)
			);
		endforeach;
		
		return $output;
	}
}