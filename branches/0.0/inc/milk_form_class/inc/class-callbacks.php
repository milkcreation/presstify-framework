<?php

class MKCF_Callbacks{
	public	$order,
			$functions;
	
	public function __construct(MKCF $master) {
        $this->mkcf = $master;

		$functions = array(			
			'addon_set_form_options',
			'addon_set_field_options',
			
			'field_set',
			'field_value',
			'field_before_display',
			'field_type_output_display',
			'field_output_display',
			
			'form_set_options',
			'form_before_display',
			'form_before_output_display',	// Modification du pré-affichage de formulaire
			'form_after_output_display',	// Modification du post-affichage de formulaire
			'form_output_display',
			'form_buttons_display',	
			
			'handle_get_request',
			'handle_before_redirect',
			'handle_redirect',
			'handle_parse_request',
			'handle_parse_submit',
			'handle_check_required',
			'handle_check_request'			
						
		);
		
	}
	
	/**
	 * Définition des fonctions de callback
	 */
	 function set( $hookname, $type, $name, $function, $priority ){
		if( ! isset( $this->functions[$hookname][$type][$priority][$name] ) )
			$this->functions[$hookname][$type][$priority][$name] = array( $function );
		else 
			array_push( $this->functions[$hookname][$type][$priority][$name], $function );	
	 }

	/**
	 * Définition des fonctions de callback au travers des addons
	 */
	 function addons_set( $hookname, $name, $function, $priority = 10 ){
	 	$this->set( $hookname, 'addons', $name, $function, $priority );	 	
	 }
	 
	/**
	 * Définition des fonctions de callback au travers des types de champ
	 */
	 function field_type_set( $hookname, $name, $function, $priority = 10 ){
	 	$this->set( $hookname, 'field_type', $name, $function, $priority );
	 }
	 
	 /**
	  * Execution des fonctions de callback
	  */
	function call( $hookname, $args = array() ){
		// Bypass
		if( empty( $this->functions[$hookname] ) )
			return;
	
		foreach( (array) $this->functions[$hookname] as $type => $priorities ) :
			switch( $type ) :
				case 'addons' :	
					ksort( $priorities );
					foreach( (array) $priorities as $priority ) :			
						foreach( (array) $priority as $name => $functions ) :							
							if( ! $this->mkcf->addons->is_form_active( $name ) )
								continue;
							foreach( (array) $functions as $function )	:					
								call_user_func_array( $function, $args );
							endforeach;
						endforeach;
					endforeach;
					break;
				case 'field_type' :	
					ksort( $priorities );
					foreach( (array) $priorities as $priority ) :			
						foreach( (array) $priority as $name => $functions ) : 
							if( ! $this->mkcf->fields->types->has_type( $name ) )
								continue;
							foreach( (array) $functions as $function ) :						
								call_user_func_array( $function, $args );
							endforeach;
						endforeach;
					endforeach;
					break;
			endswitch;
		endforeach;
	}	
}	