<?php
/**
 * Méthodes de traitement des champs de formulaire
 */
class MKCF_Fields{
	var $default_attrs,
		$types;
			
	public function __construct(MKCF $master) {
        $this->mkcf	= $master;
		// Définition des attributs par défaut
		$this->default_attrs = $this->default_attrs();	
		// Initialisation des types de champ
		$this->types  = new MKCF_Field_Types( $this );		
    }	
	
	/**
	 * Récupération des attributs par défaut
	 */
	public function default_attrs(){
		return array(			
			// Classes			
			'container_class' 	=> '%s',
			'label_class' 		=> '%s',
			'field_class' 		=> '%s',
			'group'				=> 0,			
			'order'				=> 0,
			// Attributs label
			'label'				=> true,
			// Attributs du champ de saisie
			'type'				=> 'text',	
			'name'				=> '%s',				
			'tabindex'			=> 0,
			'readonly'			=> false,				
			'value'				=> '',
			'choices'			=> array(),
			'choice_none' 		=> '',
			'choice_all' 		=> '',			
			'integrity_cb' 		=> false, // string | array( 'function' => [function_name], 'args' => array( $arg1, $arg2, ... ), 'error' => 'message d'erreur personnalisé' )
			'autocomplete'		=> 'on',
			'onpaste'			=> true, // Autoriser le copier/coller
			// Attributs HTML5 du champ de saisie
			'placeholder'		=> '',
			'required'			=> false,  // bool | string : Message d'erreur personnalisé /** @todo array( 'tagged' => true, 'check' => true, html5 => true, 'error' => 'message d'erreur perso' ); **/
			'pattern'			=> false,
			// Addons et options
			'step'				=> 1,
			'add-ons'			=> array(),
			'options'			=> array(),
			'echo'				=> false
		);
	}
	
	/**
	 * Initialisation
	 */
	public function init(){		
		$this->set_fields( $this->mkcf->forms->current['fields'] );
		
		// Tri des champs
		$positions = array(); $groups = array(); $position_order = array(); $group_order = array();	
		/// Définition des valeurs maximum
		foreach ( (array) $this->mkcf->forms->current['fields'] as $params ) :
			$positions[] = $params['order']; $groups[] = $params['group'];
		endforeach;
		$position_max = max( $positions );  $group_max = max( $groups );
		
		foreach ( (array) $this->mkcf->forms->current['fields'] as $key => $params ) :
			if( ! $params['order'] ) $this->mkcf->forms->current['fields'][$key]['order'] = ++$position_max;
			if( ! $params['group'] ) $this->mkcf->forms->current['fields'][$key]['group'] = $group_max+1;
			$position_order[$key] = $this->mkcf->forms->current['fields'][$key]['order']; 
			$group_order[$key] = $this->mkcf->forms->current['fields'][$key]['group'];
		endforeach;			
		@array_multisort( $group_order , $position_order, $this->mkcf->forms->current['fields'] );

		
		foreach( $this->mkcf->forms->current['fields'] as &$field ) :
						
		endforeach;		
	}	

	/**
	 * Définition des tous les champs d'un formulaire
	 * 
	 * @param array $fields Tableau indexé des champs de formulaire
	 * @see $this->set_field pour connaître la syntaxe des champs de formulaire 
	 */
	public function set_fields( &$fields = array() ){
		foreach( $fields as $index => &$field ) :
			$field['index'] = $index;
			$field = $this->set_field( $field );	
		endforeach;
	}
	
	/**
	 * Définition d'un champ de formulaire
	 * 
	 * @param array $field Tableau dimensionné d'un champ de formulaire
	 */
	public function set_field( $field = array() ){				
		$field = $this->mkcf->functions->parse_options( $field, $this->default_attrs );
		
		// Gestion des étapes ( 1 par défaut )
		if( $this->mkcf->forms->get_option( 'bystep' ) )
			$field['step'] = ! empty( $field['step'] ) ? $field['step'] : 1;
		
		// Définition des options		
		$field['options'] = $this->set_options( $field );
		
		// Incrémentation des liste choix
		if( $field['choices'] && is_array( $field['choices'] ) ) :
			array_unshift( $field['choices'], null );
			unset( $field['choices'][0] ) ;
		endif;
		
		// Concaténation des attributs de champ protégés
		$field['form_id'] = $this->mkcf->forms->current['ID'];
		$field['form_prefix'] = $this->mkcf->forms->current['prefix'];			
		$field['slug'] = ! isset( $field['slug'] )? "field-slug_".$field['form_id']."-".$field['form_prefix']."-".$field['index'] : $field['slug'];
		$field['name'] = $this->get_name( $field );
		
		// Option par defaut des addons
		$field['add-ons'] = $this->mkcf->addons->set_field_options( $field['add-ons'] );		
		
		$this->mkcf->callbacks->call( 'field_set', array( &$field, $this->mkcf ) );
		
		return $field;
	}

	/**
	 * Définition des options des champs
	 */
	public function set_options( $field ){
		if( ( $type_datas = $this->types->get_type_datas( $field['type'] ) ) && ( isset( $type_datas['options'] ) ) )
			$field['options'] = $this->mkcf->functions->parse_options( $field['options'], $type_datas['options'] );
		
		return $field['options'];
	}
	
	/**
	 * Récupération des champs à afficher pour le formulaire courant
	 *  
	 * @return mixed Liste des champs 
	 */	
	public function get_fields_displayed( ){
		// Bypass
		if( ! $_form = $this->mkcf->forms->get_current( ) )
			return;
		
		return $_form['_fields'];
	}
			
	/**
	 * Récupération de la liste des champs pour un formulaire
	 *
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant
	 * 
	 * @return array Un tableau indexé de tous les champs et leurs attributs pour le formulaire requis 
	 */
	public function get_list( $form = null ){
		// Bypass
		if( ! $_form = $this->mkcf->forms->get( $form ) )
			return;
		
		if( isset( $_form['fields'] ) )
			return $_form['fields'];
	}	

	/**
	 * Récupération d'un champ selon son un attribut
	 * 
	 * @param string $attr attribut de champs @this->set_field pour connaître la liste
	 * @param string $value valeur de l'attribut
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant
	 * 
	 * @return array Un tableau dimensionné du champ et de ses attributs pour le formulaire requis
	 */
	public function get_by( $attr, $value, $form = null ){
		// Bypass
		if( ! $_form = $this->mkcf->forms->get( $form ) )
			return;
		
		foreach( $_form['fields'] as $field )
			if( isset( $field[$attr] ) && ( $field[$attr] == $value ) )
				return $field;
	} 
	
	/**
	 * Récupération d'un champ selon son slug
	 * 
	 * @param string $slug valeur du slug
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant
	 * 
	 * @return array Un tableau dimensionné du champs et de ses attributs pour le formulaire requis
	 */
	public function get_by_slug( $slug, $form = null ){
		return $this->get_by( 'slug', $slug, $form );
	}
	
	/**
	 * Récupération de l'index d'un champs selon son slug
	 * 
	 * @param string $attr attribut de champs @see this->set_field pour connaître la liste
	 * @param string $value valeur de l'attribut
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant
	 * 
	 * @return null|int Valeur de l'index du champ pour le formulaire requis
	 */
	private function get_index_by( $attr, $value, $form = null ){
		// Bypass
		if( ! $fields = $this->get_list( $form ) )
			return;	
		
		foreach( $fields as $index => $field )
			if( isset( $field[$attr] ) && ( $field[$attr] == $value ) )
				return $index;
	}
	
	/**
	 * Récupération de l'index d'un champs selon son slug
	 * 
	 * @param string $slug valeur du slug
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant
	 * 
	 * @return null|int Valeur de l'index du champ pour le formulaire requis
	 */
	private function get_index_by_slug( $slug, $form = null ){
		return $this->get_index_by( 'slug', $slug, $form );
	}
	
	/**
	 * Récupération du nom d'un champ
	 * 
	 * @param array $field Tableaux dimensionné de champ
	 * 
	 * @return string Valeur du champ pour le formulaire requis
	 */
	public function get_name( $field ){
		return sprintf( $field['name'] , "{$field['form_prefix']}[{$field['form_id']}][{$field['slug']}]" );
	}
	
	/**
	 * Récupération du nom d'un champ selon son slug
	 * 
	 * @param string $slug valeur du slug
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant
	 * 
	 * @return string Valeur du champ pour le formulaire requis
	 */
	public function get_name_by_slug( $slug, $form = null ){
		// Bypass
		if( ! $_form = $this->mkcf->forms->get( $form ) )
			return;
		
		foreach( $_form['fields'] as $field )
			if( in_array($slug , $field) )
				return sprintf($field['name'] , "{$_form['prefix']}[{$_form['ID']}][".$slug."]" );
			
		return false;
	}	
	
	/**
	 * Récupération de la valeur pour un champs
	 */
	public function get_value( $field ){
		if( ( $this->mkcf->handle->parsed_request['form_id'] == $field[ 'form_id' ] ) && ( isset( $this->mkcf->handle->parsed_request['values'][ $field['slug'] ] ) ) ) :
			$field_value = $this->mkcf->handle->parsed_request['values'][ $field['slug'] ];
		else :
			$field_value = $field['value'];
		endif;		
		
		$this->mkcf->callbacks->call( 'field_value', array( &$field_value, $field, $this->mkcf ) );

		return $field_value;
	}
		
	/**
	 * Translation des valeurs pour les choix
	 */
	public function translate_value( $value, $choices, $field ){		
		foreach( (array) $choices as $index => $label ) :				
			if( ! empty( $field['choice_none'] ) && ( empty( $value ) ) ):
				$value = $field['choice_none'];
			elseif( ! empty( $field['choice_all'] ) && ( (int) $value == -1 ) ) :
				$value = $field['choice_all'];
			elseif( $value == $index ) :				
				$value = $label;
			endif;
		endforeach;	
		
		return $value;
	}
	
	/**
	 * AFFICHAGE
	 */
	/**
	 * Affichage d'un champs de formulaire
	 */
	public function display( $field, $args = array() ){
		$field 	= $this->mkcf->functions->parse_options( $args, $field );
		// Type d'affichage du champ
		$field['display'] = false;
		
		// Fonction de court-circuitage des attributs de champs avant l'affichage
		$this->mkcf->callbacks->call( 'field_before_display', array( &$field ) );
		
		// Récupération des options de formulaire
		$form_options = $this->mkcf->forms->get_options( $field[ 'form_id' ] );		
		
		// Gestion de l'instance d'erreur
		static $err_inst;
		if( ! $err_inst ) $err_inst = 0;
		$has_error = ( $this->mkcf->errors->field_has( $field ) && ( $err_inst < $this->mkcf->errors->showed ) ) ? 'has_error' : '';		
		
		$output  = "";
		
		// Pré-Affichage du formulaire	
		if( isset( $field['options']['before'] ) )
			$output .= $field['options']['before'];
		
		// Récupération de la valeur du champ		
		$field['value'] = $this->get_value( $field );

		if( $field['type'] == 'hidden' )
			return 	"\n<input type=\"hidden\" name=\"". esc_attr( $this->get_name( $field ) ) ."\" class=\"field field-". $field['slug'] ."\" value=\"". esc_attr( stripslashes( $field['value'] ) )."\" />";	
		
		// Classe du container		
		$field_class = sprintf( $field['container_class'], "field-wrapper field-wrapper-".$field['type']." field-wrapper-".$field['slug']." field-wrapper-".$field['form_id']."-".$field['slug']." ".$has_error ); 
		if( $field['required'] )
			$field_class .= " field-required";
		
		// Ouverture du wrapper
		if( ! $this->types->type_supports( 'nowrapper', $field['type'] ) )
			$output .= "\n<div class=\"{$field_class}\">";
		switch( $field['type'] ) :
			case 'html' :
				$output .= sprintf( $field['html'], "{$field['form_prefix']}[{$field['form_id']}][".$field['slug']."]", $field['label'], $field['value'] );
				break;
			case 'string' :
				$output .= sprintf( $field['html'], "{$field['form_prefix']}[{$field['form_id']}][".$field['slug']."]", $field['label'], $field['value'] );
				break;				
			case 'textarea' :		
			case 'input' :
			case 'password' :
			case 'checkbox' :
			case 'radio' :
			case 'dropdown' :	
				$name = $this->get_name( $field );				
				// Intitulé (Label)
				if( $this->types->type_supports( 'label', $field['type'] ) && ! empty( $field['label'] ) ) :
					$label_class = sprintf( $field['label_class'], "field-label field-label-".$field['type']." field-label-".$field['slug']." field-label-".$field['form_id']."-".$field['slug'] );
					$output .= "\n\t<label for=\"field-{$field['form_id']}-{$field['slug']}\" class=\"".$label_class."\">";
					$output .= $field['label'];					
					if( $field['required'] ) 
						$output .= "<span class=\"required\">*</span>";
					$output .= "</label>";
				endif;
				
				// Encapsulation des choix
				if( in_array( $field['type'], array( 'radio', 'checkbox' ) ) )
					$output .= "<div class=\"choices-wrapper\">";
					
				//Ouverture de balise ouvrante du champ de saisie								
				switch( $field['type'] ) :
					case 'input' :
						$output .= "\n\t<input type=\"text\" value=\"". esc_attr( $field['value'] ) ."\"";
						break;
					case 'password' :
						$output .= "\n\t<input type=\"password\" value=\"". esc_attr( $field['value'] )."\"";
						break;
					case 'textarea' :
						$output .= "\n\t<textarea";
						break;
					case 'dropdown' :
						$output .= "\n\t<select";
						break;
				endswitch;
				// Attributs (name, id, class )
				if( ! in_array( $field['type'], array( 'checkbox', 'radio' ) ) ) :
					$field_class = rtrim( trim( sprintf( $field['field_class'], "field field-{$field['form_id']} field-{$field['slug']}") ) );
					$output .= " name=\"". esc_attr( $name ) ."\" id=\"field-{$field['form_id']}-{$field['slug']}\" class=\"".$field_class."\"";
				endif;
				
				if( $this->types->type_supports( 'placeholder', $field['type'] ) && $field['placeholder'] )
					if( is_bool($field['placeholder']) )
						$output .= " placeholder=\"".$field['label']."\"";
					elseif( is_string( $field['placeholder']) )
						$output .= " placeholder=\"".$field['placeholder']."\"";
				//Autocomplete des champs de saisie
				if( in_array( $field['type'], array( 'input', 'password', 'dropdown', 'textarea' ) ) )	
					$output .= " autocomplete=\"{$field['autocomplete']}\"";
				// Champs en lecture seule
				if( in_array( $field['type'], array( 'input', 'password', 'dropdown', 'textarea' ) ) && $field['readonly'] )
					$output .= " readonly=\"readonly\"";
				// Autoriser le copier/coller
				if( in_array( $field['type'], array( 'input', 'password', 'dropdown', 'textarea' ) ) && ! $field['onpaste'] )	
					$output .= " onpaste=\"return false;\""; 
				//Fermeture de balise ouvrante du champ de saisie
				if( in_array( $field['type'], array( 'input', 'password', 'dropdown' ) ) ) :
					$output .= "/>";		
				elseif( $field['type'] == 'textarea' ) :
					$output .= "/>";
				endif;
				
				if( $this->types->type_supports( 'choices', $field['type'] ) ) :					
					if( $field['type'] =='dropdown' && $field['choice_all'] )
						$output .= "<option value=\"". esc_attr( -1 ) ."\" ".selected( empty($field['value']), true, false ).">{$field['choice_all']}</option>";
					if( $field['type'] =='dropdown' && $field['choice_none'] )
						$output .= "<option value=\"". esc_attr( 0 ) ."\" ".selected( empty($field['value']), true, false ).">{$field['choice_none']}</option>";					

					// Lecture seule des cases à cocher et boutons radio
					if( ( $field['type'] == 'checkbox' ) && $field['readonly'] ) :
						if( empty( $field['value'] ) )
							$output .= "<input type=\"hidden\" name=\"". esc_attr( $name ) ."[]\" value=\"\" />";
						else
							foreach( (array) $field['value'] as $val )
								$output .= "<input type=\"hidden\" name=\"". esc_attr( $name )."[]\" value=\"{$val}\" />";
					elseif( ( $field['type'] == 'radio' ) && $field['readonly'] ) :
						$output .= "<input type=\"hidden\" name=\"". esc_attr( $name ) ."\" value=\"". ( $field['value'] ? esc_attr( $field['value'] ) : "" )."\" />";
					endif;
					
					foreach( (array) $field['choices'] as $ovalue => $label ) :
						switch( $field['type'] ) :
							case 'dropdown' :
								$output .= "<option value=\"". esc_attr( $ovalue ) ."\" ".selected( $field['value'] == $ovalue, true, false ).">{$label}</option>";
								break;
							case 'checkbox' :								
								$output .= "<label class=\"choice-title\"><input type=\"checkbox\" value=\"". esc_attr( $ovalue ) ."\" name=\"". esc_attr( $name )."[]\" ".checked( ( is_array( $field['value'] ) && in_array( $ovalue, $field['value']) ), true, false )." autocomplete=\"{$field['autocomplete']}\"". ( $field['readonly'] ? " disabled=\"disabled\" " : "" ) ."/>$label</label>";
								break;
							case 'radio' :
								$output .= "<label class=\"choice-title\"><input type=\"radio\" value=\"". esc_attr( $ovalue ) ."\" name=\"". esc_attr( $name )."\" ".checked($field['value']==$ovalue, true, false )." autocomplete=\"{$field['autocomplete']}\"". ( $field['readonly'] ? " disabled=\"disabled\" " : "" ) ."/>$label</label>";
								break;			
						endswitch;
					endforeach;			
				endif;
				
				// Fermeture de l'encapsulation des choix
				if( in_array( $field['type'], array( 'radio', 'checkbox' ) ) )
					$output .= "</div>";
								
				//Balise fermante du champ de saisie				
				switch( $field['type'] ) :
					case 'textarea' : 						
						$field['value']	= trim( strip_tags( html_entity_decode( $field['value'] ) ), "\t\n\r\0\x0B." );
						$output .= esc_attr( $field['value'] ) ."</textarea>";
						break;
					case 'dropdown' :
						$output .= "</select>"; 
						break;
				endswitch;					
				break;				
			default :
				// Intitulé (Label)
				if( $this->types->type_supports( 'label', $field['type'] ) && ! empty( $field['label'] ) ) :
					$label_class = sprintf( $field['label_class'], "field-label field-label-".$field['type']." field-label-".$field['slug']." field-label-".$field['form_id']."-".$field['slug'] );
					$output .= "\n\t<label for=\"field-{$field['form_id']}-{$field['slug']}\" class=\"".$label_class."\">";
					$output .= $field['label'];
				
					if( $field['required'] ) 
						$output .= "<span class=\"required\">*</span>";
					$output .= "</label>";
				endif;
				
				$this->mkcf->callbacks->call( 'field_type_output_display', array( &$output, $field, $this->mkcf ) );
				break;					
		endswitch;
		
		// Affichage des erreurs de formulaire
		if( $has_error && $form_options['errors']['field'] )
			$output .= "<div class=\"field-error\">".$this->mkcf->errors->field_display( $field )."</div>";		
		
		// Fermeture du wrapper
		if( ! $this->types->type_supports( 'nowrapper', $field['type'] ) )
			$output .= "\n</div>";		
		
		// Post-Affichage du formulaire		
		if( isset( $field['options']['after'] ) )
			$output .=  $field['options']['after'];
		
		// Incrémentation de l'instance d'erreur
		if( $this->mkcf->errors->field_has( $field ) )
			$err_inst++;
				
		// Fonction de court-circuitage de l'affichage du champ
		$this->mkcf->callbacks->call( 'field_output_display', array( &$output, $field ) );		
				
		if( $field['echo'] )
			echo $output;
		else
			return $output;
	}
	
	/**
	 * ACTIONS SUR LES TYPES DE CHAMPS
	 */
	/**
	 * Définition d'un nouveau type de champs
	 * 
	 * @param array $args Attributs du type
	 * @see MKCF_Field_Types::set_type
	 */
	public function set_type( $args = array() ){
		$this->types->set_type( $args );
	}	
}

/**
 * Méthodes de traitement des types de champ de formulaire
 */
class MKCF_Field_Types{
	var	$fields, // Appel aux méthodes des champs
		$sections,
		$types;
		
	public function __construct( $fields ){
		$this->fields = $fields;
        $this->set_sections();
		$this->set_types();		
    }
	
	/**
	 * Initialisation des addons
	 */
	public function init( $field_types ){
		foreach( $field_types as $field_type_path ) 
			if( file_exists( $field_type_path ) )
				require_once( $field_type_path );		
	}
	
	/**
	 * Définition des sections
	 */
	 private function set_sections( $sections = array() ){
	 	$defaults = array(
			'text' => __( 'Chaines de caractères', 'tify' ),
			'input-fields' => __( 'Champs de saisie', 'tify' ), 
			'selection' => __( 'Listes de selection', 'tify' ),
			'misc' => __( 'Eléments riche', 'tify' )
		);
		$this->sections = wp_parse_args( $sections, $defaults );
	 }
	 
	/**
	* Définition des types de champs
	*/
	private function set_types(){
		$defaults = array(
			array(
				'slug'			=> 'html',
				'label' 		=> __( 'HTML', 'tify' ),
				'section' 		=> 'text',
				'order' 		=> 1,
				'supports'		=> array( )				 
			),
			array(
				'slug'			=> 'string',
				'label' 		=> __( 'String', 'tify' ),
				'section' 		=> 'text',
				'order' 		=> 1,
				'supports'		=> array( )				 
			),
			array(
				'slug'			=> 'input',
				'label'			=> __( 'Text Input', 'tify' ),
				'section' 		=> 'input-fields',
				'order' 		=> 1,
				'supports'		=> array( 'label', 'placeholder', 'integrity-check', 'request' )
			),
			array(
				'slug'			=> 'textarea',
				'label'			=> __( 'Textarea', 'tify' ),
				'section' 		=> 'input-fields',
				'order' 		=> 2,
				'supports'		=> array( 'label','placeholder', 'integrity-check', 'request' )
			),
			array(
				'slug'			=> 'password',
				'label'			=> __( 'Password', 'tify' ),
				'section' 		=> 'input-fields',
				'order' 		=> 3,
				'supports'		=> array( 'label', 'placeholder', 'integrity-check', 'request' )
			),
			array(
				'slug'			=> 'hidden',
				'label' 		=> __( 'Hidden (embedded datas)', 'tify' ),
				'section' 		=> 'input-fields',
				'order' 		=> 4,
				'supports'		=> array( 'request' )
			),
			array(
				'slug'			=> 'radio',
				'label' 		=> __( 'Radio Button', 'tify' ),
				'section' 		=> 'selection',
				'order' 		=> 1,
				'supports'		=> array( 'label', 'choices', 'integrity-check', 'request' )
			),
			array(
				'slug'			=> 'checkbox',
				'label' 		=> __( 'Checkbox', 'tify' ),
				'section' 		=> 'selection',
				'order' 		=> 2,
				'supports'		=> array( 'label', 'choices', 'multiselect', 'integrity-check', 'request' )
			),
			array(
				'slug'			=> 'dropdown',
				'label' 		=> __( 'Dropdown', 'tify' ),
				'section' 		=> 'selection',
				'order' 		=> 3,
				'supports'		=> array( 'label', 'choices', 'integrity-check', 'request' )
			),
			array(
				'slug'			=> 'select-multiple',
				'label' 		=> __( 'Multi selection list', 'tify' ),
				'section' 		=> 'selection',
				'order' 		=> 4,
				'supports'		=> array( 'label', 'choices', 'multiselect', 'integrity-check', 'request' )
			)	
		/*	
		 	array(
			  	'slug'			=> 'wysiwyg',
				'label'			=> __( 'Text editor', 'tify' ),
				'section' 		=> 'misc',
				'order' 		=> 1,
				'supports'		=> array( )
			),
			array(
				'slug'			=> 'datepicker',
				'label' 		=> __( 'Datepicker', 'tify' ),
				'section' 		=> 'misc',
				'order'			=> 2,
				'supports'		=> array( )
			),
		 	array(
				'slug'			=> 'colorpicker',
				'label' 		=> __( 'Colorpicker', 'tify' ),
				'section' 		=> 'misc',
				'order'			=> 3
			),
			array(
				'slug'			=> 'autocomplete',
				'label' 		=> __( 'Autocomplete text field', 'tify' ),
				'section' 		=> 'misc',
				'order'			=> 4,
				'supports'		=> array( )
			),
			array(
				'slug'			=> 'combobox',
				'label' 		=> __( 'Combobox of text input and dropdown', 'tify' ),
				'section' 		=> 'misc',
				'order'			=> 5,
				'supports'		=> array( )
			)*/					
		);
				
		return $this->types = $defaults;
	}

	/**
	 * 
	 */
	public function set_type( $type = array() ){
		array_push( $this->types, $type );
	}
	
	/**
	 * 
	 */
	public function has_type( $type, $form = null ){
		if( ! $fields = $this->fields->mkcf->fields->get_list( $form ) )
			return false;
		foreach( $fields as $f )
			if( $f['type'] == $type )
				return true;
		return false;
	}

	/**
	 * 
	 */
	public function get_forms_has_type( $type ){
		$forms = array();
		if( ! $this->fields->mkcf->forms->get_list() )
			return;
		foreach( $this->fields->mkcf->forms->get_list() as $form )
			if( $this->has_type( $type, $form ) )
				$forms[] = $form['ID'];
		
		return $forms;
	}
	
	/**
	 * Récupération des données d'un type
	 */
	public function get_type_datas( $slug = '' ){
		foreach(  $this->types as  $type )
			if( $type['slug'] == $slug )
				return $type;
	}
	
	/**
	 * Récupération de l'intitulé d'un type
	 */ 
	private function get_type_label( $slug ){
		if( $type = $this->get_type_datas( $slug ) )
			return $type['label'];
	}
	
	/**
	 * Vérification de support d'un type
	 */
	public function type_supports( $attr = '', $type ){
		if( $type = $this->get_type_datas( $type ) )
			return in_array( $attr, $type['supports'] );
	}
	
	/**
	 * Récupération des types d'une section
	 */
	private function types_by_section( $section ){
	 	$_types = array();
	 	foreach( $this->types as $type )
			if( $section === $type['section'] )
				$_types[] = $type;
		return $_types;	
	}
}