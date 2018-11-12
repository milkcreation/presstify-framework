<?php
class tify_forms_field_dynamic{
	private $mkcf;
	
	/**
	 * Initialisation
	 */
	function __construct( MKCF $mkcf ){
		// MKCF	
		$this->mkcf 	= $mkcf;
				
		// Définition du type de champ
		$this->set_type();
		
		// Actions et Filtres Wordpress
		// ...
		
		// Callbacks
		//@ TODO $this->mkcf->callbacks->field_type_set( 'field_set', 'dynamic', 'cb_field_set' );
		$this->mkcf->callbacks->field_type_set( 'form_buttons_display', 'dynamic', array( $this, 'cb_form_buttons_display' ) );
		$this->mkcf->callbacks->field_type_set( 'handle_parse_submit', 'dynamic', array( $this, 'cb_handle_parse_submit' ) );
	}
	
	/**
	 * Déclaration du type de champ
	 */
	function set_type(){
		$this->mkcf->fields->set_type(
			array( 
				'slug'			=> 'dynamic',
				'label' 		=> __( 'Champ dynamique', 'mktzr_forms' ),
				'section' 		=> 'misc',
				'order'			=> 99,
				'supports'		=> array( 'request', 'nowrapper' ),
				'options'		=> array(
				'type' 				=> 'html',
					'init' 			=> 1,
					'start' 		=> 0,
					'add_by'		=> 1,
					'buttons' 		=> array(
						'add' => array(
							'title' => __( 'Ajouter', 'mktzr_forms' )
						)
					)
				)
			)
		);
	}
	
	/**
	 * Définition du champ
	 * @TODO Conservation des valeurs soumises ??
	 */
	function cb_field_set( &$field ){
		if( $field['type'] != 'dynamic' )
			return;
		
		// Définition de la quantité de champ à afficher
		$amount = ( ( $cache = $this->mkcf->handle->get_cache() ) && isset( $cache['fields']['dynamic'][$field['slug']]['amount'] ) ) ? $cache['fields']['dynamic'][$field['slug']]['amount'] : $field['options']['init'];
		$i = $field['options']['start'];
		while( $i < ( $amount + $field['options']['start'] ) ) :
			$_fields[] = array_merge(
				$field,
				array(
					'type' => $field['options']['type'],
					'label' => sprintf( $field['label'], $i ),
					'slug' => $field['slug'].'-'.$i,
					'name' => '%s',
					'value' => $this->mkcf->fields->get_value( $field ),
					'dynamic' => $field['slug']
				)
			);
			$i++;				
		endwhile;
		$this->mkcf->handle->set_cache( array(
				'dynamic-'.$field['slug'] => $field['options']['add_by']
			)			
		);
	}
	
	/**
	 * Affichage du bouton d'ajout de champs 
	 */
	function cb_form_buttons_display( &$output ){
		$has_field = false;
		$dynamics = array();
		foreach( ( array ) $this->mkcf->fields->get_fields_displayed() as $f ) :
			if( ! isset( $f['dynamic'] ) ) continue;
			if( in_array( $f['dynamic'], $dynamics ) ) continue;
			$dynamics[] = $f['dynamic'];
			$has_field = true;
		endforeach;
		
		if( ! $has_field )
			return;
		
		$_form['ID'] = $this->mkcf->forms->get_ID();
		$_form['prefix'] = $this->mkcf->forms->get_prefix();
		$button = "";
	
		foreach( $dynamics as $dynamic ) :
			$button .= "<div class=\"buttons-group dynamic-buttons\">\n";
			$button .= "\t<button type=\"submit\" id=\"dynamic_add-{$_form['prefix']}-{$_form['ID']}\" class=\"dynamic\" name=\"submit-{$_form['prefix']}-{$_form['ID']}\" value=\"dynamic_add:{$dynamic}\">".__( 'Ajouter', 'mktzr_forms')."</button>\n";
			$button .= "\t</div>\n";
		endforeach;
	
		$output = $button . $output;
	}

	/**
	 * 
	 */
	function cb_handle_parse_submit( $continue, $submit ){
		// Bypass
		if( ! preg_match( '/dynamic_add:/', $submit ) )
			return $continue;
		if( ! preg_match( '/dynamic_add:(.*)/', $submit, $matches ) )
			return $continue;
		if( ! $field = $this->mkcf->fields->get_by_slug( $matches[1] ) )
			return $continue;
		
		$amount = ( ( $cache = $this->mkcf->handle->get_cache() ) && isset( $cache['fields']['dynamic'][$field['slug']]['amount'] ) ) ? $cache['fields']['dynamic'][$field['slug']]['amount'] : $field['options']['init'];
		$args['fields']['dynamic'][$field['slug']]['amount'] = $amount + $field['options']['add_by'];
		$this->mkcf->handle->set_cache( $args );
		$this->mkcf->forms->step_form_datas();
		
		return false;	
	}
}