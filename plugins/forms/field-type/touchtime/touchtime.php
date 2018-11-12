<?php
/**
 * @name FORMS - TOUCHTIME
 * @description Champs de formulaire date
 * 
 * @package Milk_Thematzr
 * @subpackage Forms
 * 
 * @usage
 * 
	array(
		'ID' => #,
		'title' => 'Sample de Formulaire',
		'prefix' => 'sample',
		'fields' => array(
			array(
				'slug'			=> 'birthday',
 				'label'			=> __('Date d\'anniversaire' ), 				
				'type'			=> 'touchtime'
			)
 	... 
 * 
 */
class tify_forms_field_touchtime{
	private $mkcf;
	
	/**
	 * Initialisation
	 */
	function __construct( MKCF $mkcf ){
		// MKCF	
		$this->mkcf = $mkcf;
		
		// Déclaration du type de champs
		$this->set_type();
		
		// Actions et Filtres Wordpress
		// ...
		
		// Callbacks
		$this->mkcf->callbacks->field_type_set( 'field_type_output_display', 'touchtime', array( $this, 'cb_field_type_output_display' ) );
		$this->mkcf->callbacks->field_type_set( 'handle_parse_request', 'touchtime', array( $this, 'cb_handle_parse_request' ) );			
	}
	
	/**
	 * Déclaration du type de champs
	 */
	function set_type(){
		$this->mkcf->fields->set_type(
			array( 
				'slug'			=> 'touchtime',
				'label' 		=> __( 'Date', 'mktzr_forms' ),
				'section' 		=> 'misc',
				'order'			=> 99,
				'supports'		=> array( 'label', 'request' )
			)
		);
	}
	
	/**
	 * MKCF CALLBACKS
	 */
	/**
	 * Affichage du champ
	 */	 
	function cb_field_type_output_display( &$output, $field ){
	 	$output .= mk_touch_time( 
			array(
				'echo' 		=> false, 
				'name' 		=> $this->mkcf->fields->get_name( $field ),
				'selected' 	=> esc_attr( $this->date2mysql( $field['value'] ) )
			) 
		);
	}
	
	/**
	 * Fonction de court-circuitage de la valeur de requête - Translation de la valeur au format SQL
	 */
	function cb_handle_parse_request( &$field ){
		// Bypass
		if( $field['type'] != 'touchtime' )
			return;
		
		$field['value'] = esc_attr( $this->date2mysql( $field['value'] ) );
	}

	/**
	 * CONTROLEURS
	 */
	/**
	 * Translation des données de date au format SQL
	 */
	function date2mysql( $date ){
		if( is_array( $date ) && isset( $date['jj'] ) && isset( $date['mm'] ) && isset( $date['aa'] ) && isset( $date['hh'] ) && isset( $date['mn'] ) && isset( $date['ss'] ) )
			return $date['aa']."-".$date['mm']."-".$date['jj']." ".$date['hh'].":".$date['mn'].":".$date['ss'];
		return $date;
	}
}
