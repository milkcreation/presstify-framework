<?php
/**
 * Configuration :
 	...
 	array(
		'ID' 		=> {form_id},
		'title' 	=> '{form_title}',
		'prefix' 	=> '{form_prefix}',
		'fields' 	=> array(
			...
			array(
				'slug'			=> '{field_slug}',
				'label' 		=> '{field_label}',
				'type' 			=> 'tify_dropdown',
			),
			...
		)
	)
	... 
 */

class tify_forms_field_tify_dropdown{
	/* = CONSTRUCTEUR = */
	function __construct( MKCF $mkcf ){
		// MKCF	
		$this->mkcf 	= $mkcf;
				
		// Déclaration du type de champs
		$this->register_field_type();		
		
		// Callbacks
		$this->mkcf->callbacks->field_type_set( 'field_type_output_display', 'tify_dropdown', array( $this, 'cb_field_type_output_display' ) );	
	}
	
	/* = CONTROLEURS = */
	/** == Définition du type de champ == **/
	function register_field_type(){		
		$this->mkcf->fields->set_type(
			array(
				'slug'			=> 'tify_dropdown',
				'label' 		=> __( 'tiFy Dropdown', 'tify' ),
				'section' 		=> 'misc',
				'supports'		=> array( 'label', 'integrity-check', 'request' )
			)
		);
	}

	/* = CALLBACK MKCF = */	
	/** == Affichage du champ == **/
	function cb_field_type_output_display( &$output, $field ){
		// Bypass
		if( $field['type'] != 'tify_dropdown' )
			return;
		static $instance;
		
		if( ! $instance ) :
			tify_controls_enqueue( 'dropdown' );
		endif;

		$output .= tify_control_dropdown( array(
				'id'				=> "field-{$field['form_id']}-{$field['slug']}",
				'class'				=> rtrim( trim( sprintf( $field['field_class'], "field field-{$field['form_id']} field-{$field['slug']} tify_control_dropdown") ) ),
				'show_option_none' 	=> ! empty( $field['choice_none'] ) ? $field['choice_none'] : false,
				'name'				=> $field['name'],
				'selected'			=> $field['value'],
				'choices' 			=> $field['choices'],
				'echo'				=> false
			)
		);		
	}
}