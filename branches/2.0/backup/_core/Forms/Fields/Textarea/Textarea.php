<?php
/**
 * @Overridable 
 */
namespace tiFy\Forms\Fields\Textarea;

class Textarea extends \tiFy\Forms\Fields\Factory
{
	/* = ARGUMENTS = */
	// Identifiant
	public $ID	= 'textarea';
	
	// Support
	public $Supports = array(
		'integrity',
		'label', 
		'placeholder', 
		'request',
		'wrapper'
	);	
		
	/* = CONTROLEURS = */
	/** == Affichage == **/
	public function display()
	{
		$output = "";
		
		// Affichage du champ de saisie
		$output .= "<textarea";
		/// ID HTML
		$output .= " id=\"". $this->getInputID() ."\"";
		/// Classe HTML
		$output .= " class=\"". join( ' ', $this->getInputClasses() ) ."\"";
		/// Name		
		$output .= " name=\"". esc_attr( $this->field()->getDisplayName() ) ."\"";
		/// Placeholder
		$output .= " placeholder=\"". esc_attr( $this->getInputPlaceholder() ) ."\"";
		/// Attributs
        $output .= $this->getInputHtmlAttrs();
		/// TabIndex
		$output .= " ". $this->getTabIndex();
		$output .= ">";
		/// Value
		$output .= esc_attr( $this->field()->getValue() );
		/// Fermeture
		$output .= "</textarea>";
			
		return $output;		
	}
}