<?php
/**
 * Méthodes de traitement des addons
 */
class MKCF_Addons{
	var $addons,
		$inherit,
		$active,
		 
		$default_form_options,
		$default_field_options;
	
	public function __construct(MKCF $master) {
        $this->mkcf = $master;
    }
	
	/**
	 * Initialisation des addons
	 */
	public function init( $addons ){
		$this->addons = $addons;
		foreach( $addons as $name => $args ) 
			$this->register( $name, $args );		
	}
	
	/**
	 * Déclaration d'un addon
	 */
	public function register( $name, $args = array() ){
		$this->addons[$name] = $this->mkcf->functions->parse_options( $args, array( 'path' => '', 'options' => array() ) );
		if( file_exists( $this->addons[$name]['path'] ) )
			require_once( $this->addons[$name]['path'] );		
	}	
	
	/**
	 * Permet de récupérer les options par défaut d'un add-on
	 * 
	 * @param string $addon (requis) Intitulé de l'add-on
	 */
	public function get_options( $addon ){
		if( isset( $this->addons[$addon]['options'] ) )
			return $this->addons[$addon]['options'];
	}
	
	/**
	 * Initialisation des addons de formulaire
	 */
	public function set_form(){
		$this->set_form_options( $this->mkcf->forms->current['add-ons'] );
		// Initialisation des formulaire actifs selon leurs addons 
		foreach( $this->mkcf->forms->current['add-ons'] as $key => $addon ) 
			$this->active[$key][] = $this->mkcf->forms->current['ID'];
	}
	
	/**
	 * Définition des options d'addons par défaut des formulaire
	 */
	public function set_default_form_options( $addon, $options ){
		$this->default_form_options[$addon] = $options;
	}
			
	/**
	 * Définition des addons d'un formulaire
	 * 
	 * @param array $addons Tableau indexé des add-ons
	 * @see $this->set_addon pour connaître la syntaxe des attributs par défaut d'un add-on 
	 */
	private function set_form_options( &$addons = array() ){
		
		foreach( $addons as $key => &$addon ) :	
			if( is_string( $addon ) ) :
				unset( $addons[$key] );
				$addons[$addon] = $this->mkcf->functions->parse_options( array(), array( 'active' => true ) );
			else :
				$addon = $this->mkcf->functions->parse_options( $addon, array( 'active' => true ) );
			endif;
			
		endforeach;
		foreach( $addons as $key => &$addon ) 
			if( isset( $this->default_form_options[$key] ) )				
				$addon = $this->mkcf->functions->parse_options( $addons[$key], $this->default_form_options[$key] );
		
		$this->mkcf->callbacks->call( 'addon_set_form_options', array( &$addons, $this->mkcf ) );
	}	
	
	/**
	 * Définition des options d'addons par défaut des champs de formulaire
	 */
	public function set_default_field_options( $addon, $options ){
		$this->default_field_options[$addon] = $options;
	}
	
	/**
	 * Mise à jour des options de champs d'un add-on
	 * 
	 * @param string $addon (requis) Intitulé de l'add-on
	 */
	public function set_field_options( &$addons_field_options ){
		// Bypass
		if( ! $addons = $this->get_active_for_form( ) )
			return;
		foreach( $addons as $addon ) :
			if( ! isset( $addons_field_options[$addon] ) )	
				$addons_field_options[$addon] = array();
			if( isset( $this->default_field_options[$addon] ) )	
				$addons_field_options[$addon] = $this->mkcf->functions->parse_options( $addons_field_options[$addon], $this->default_field_options[$addon] );
		endforeach;				
		$this->mkcf->callbacks->call( 'addon_set_field_options', array( &$addons_field_options , $this->mkcf ) );
		
		return $addons_field_options;
	}
	
	/**
	 * Retourne les formulaires actifs pour un add-on
	 * 
	 * @param string $addon nom de l'add-on
	 * @return array Tableau indexés des IDs de formulaires actifs pour l'add-on requis
	 */
	public function get_forms_active( $addon ){
		if( isset( $this->active[$addon] ) )
			return $this->active[$addon];					
	}
	
	/**
	 * Retourne les formulaires actifs pour un add-on
	 * 
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant
	 * 
	 * @return array Tableau indexés des IDs de formulaires actifs pour l'add-on requis
	 */
	public function get_active_for_form( $form = null ){
		// Bypass
		if( ! $_form = $this->mkcf->forms->get( $form ) )
			return false;
		
		$active = array();
		foreach( (array) $_form['add-ons'] as $key => $addon )
			if( is_int( $key ) )
				$active[] = $addon;
			elseif( is_string( $key) )
				$active[] = $key;

		return $active;			
	}
	
	/**
	 * Verifie si un formulaire est actif pour un add-on
	 * 	
	 * @param string $addon nom de l'add-on
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant  
	 * 
	 * @return boolean Vrai si l'add-on est actif pour le formulaire requis
	 */
	public function is_form_active( $addon, $form = null ){
		// Bypass
		if( ! $_form = $this->mkcf->forms->get( $form ) )
			return false;
		
		// Récupération de la liste des formulaire actif pour cet add-on
		if( ( $forms = $this->get_forms_active( $addon ) ) && in_array( $_form['ID'], $forms ) )
			return true;
		
		return false;					
	}
		
	/**
	 * Récupération de toutes les options d'un add-on pour un formulaire.
	 * 
	 * @param string $addon nom de l'add-on
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant
	 * 
	 * @return array Tableau dimensionné des options de l'add-on pour le formulaire requis
	 */
	public function get_form_options( $addon, $form = null ){
		// Bypass
		if( ! $_form = $this->mkcf->forms->get( $form ) )
			return false;

		if( isset( $_form['add-ons'][$addon] ) )
			return $_form['add-ons'][$addon];		
	}
	
	/**
	 * Récupération d'une option d'un add-on pour un formulaire.
	 * 
	 * @param string $option nom de l'option
	 * @param string $addon nom de l'add-on	 
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant
	 * 
	 * @return array Tableau dimensionné des options de l'add-on pour le formulaire requis
	 */
	public function get_form_option( $option, $addon, $form = null ){
		// Bypass
		if( ! $_form = $this->mkcf->forms->get( $form ) )
			return false;

		if( isset( $_form['add-ons'][$addon][$option] ) )
			return $_form['add-ons'][$addon][$option];		
	}	

	/**
	 * Récupération des options d'un add-on pour un champ de formulaire.
	 * 
	 * @param array $field (requis) Tableau dimensionné du champ
	 * @param string $addon (requis) Nom de l'add-on	
	 * 
	 * @return array Tableau dimensionné des options de l'add-on pour le champs de formulaire requis
	 */
	public function get_field_options( $field, $addon ){
		if( isset( $field['add-ons'][$addon] ) )
			return $field['add-ons'][$addon];	
	}
	
	/**
	 * Récupération d'une option d'un add-on pour un champ de formulaire.
	 * 
	 * @param string $option (requis) nom de l'option
	 * @param string $addon (requis) nom de l'add-on	 
	 * @param array $field (requis) Tableau dimensionné du champ
	 * 
	 * @return array Tableau dimensionné des options de l'add-on pour le formulaire requis
	 */
	public function get_field_option( $option, $addon, $field ){
		if( isset( $field['add-ons'][$addon][$option] ) )
			return $field['add-ons'][$addon][$option];		
	}
}