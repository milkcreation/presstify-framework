<?php
/**
 * Méthodes de traitement des formulaires
 */
class MKCF_Forms{
	var $default_attrs, // Attributs optionnels par défaut des formulaires
		$forms,			// Liste des formulaires 
		$current; // Fonction de court-circuitage de l'affichage du formulaire
					
	public function __construct(MKCF $master) {
        $this->mkcf = $master;
		// Définition des attributs optionnels par défaut
		$this->default_attrs = array(
			'method'				=> 'post',
			'container_class' 		=> '%s',
			'form_class' 			=> '%s',
			'before' 				=> '',
			'after' 				=> '',
			'field_display_args' 	=> array(),
			'fields' 				=> array(),
			'add-ons'				=> array(),
			'options' 				=> array()			
		);		
    }
	
	/**
	 * Initialisation
	 */
	public function init( $forms ){
		$this->preset_forms( $forms );
		$this->set_forms( $forms );
	}
	
	/**
	 * Prédéfinition des champs de formulaire
	 * 
	 * @param $forms (requis) Tableau indexé des formulaires
	 */
	private function preset_forms( &$forms ){
		foreach( $forms as &$form ) :
			// Attributs requis
			$instance = uniqid();
			$defaults = array(
				'ID' 		=> $instance,				
				'prefix' 	=> sprintf( 'mk_custom_form-%s', $instance )
			);
			$defaults += $this->default_attrs;			
			$form = $this->mkcf->functions->parse_options( $form, $defaults );
			// Stockage du formulaire
			$this->forms[ $form['ID'] ] = $form;
		endforeach;
	}	
	
	/**
	 * Définition des formulaires
	 * 
	 * @param array $forms Tableau indexé des formulaires
	 * @see $this->set_form pour connaître la syntaxe d'un formulaire 
	 */
	public function set_forms( $forms = array() ){
		// Bypass	
		if( empty( $forms ) )
			return;
	
		foreach( $forms as $form )						
			$this->set_form( $form );				
	}
	
	/**
	 * Définition d'un formulaire
	 * 
	 * @param array $form Tableau dimensionné d'un formulaire et ses attributs
	 */
	public function set_form( $form = array() ){		
		$this->set_current( $form );
		// Initialisation des options du formulaire
		$this->init_options( $form );
		// Initialisation des add-ons
		$this->mkcf->addons->set_form();
		// Initialisation des champs de formulaire
		$this->mkcf->fields->init();	
		// Mise à jour des attributs des données de formulaire avec les données du formulaire courant
		$this->update();		
		// Réinitialisation du formulaire courant
		$this->reset_current();	
	}
	
	/**
	 * Initialisation des options de formulaire
	 */
	 public function init_options( $form = null ){
		// Attributs par défaut des options de formulaires
		$defaults = array(
			'submit' 	=> array(
				'display'	=> true,
				'label' 	=> __( 'Envoyer', 'tify' ), // Intitulé du bouton
				'before' 	=> '', // Code HTML insérer avant le bouton
				'after' 	=> '', // Code HTML insérer après le bouton
				'class'		=> ''
			),
			'errors' 	=> array(
				'title' 	=> __( 'Le formulaire contient des erreurs :', 'tify' ), // Intitulé de la liste des erreurs. Mettre à false pour masquer 
				'show'		=> -1, // Affichage des erreurs. -1 : Toutes | 0 : Aucune | n : Nombre maximum à afficher
				'teaser' 	=> '...', // Affiché seultement si toutes les erreurs ne sont pas visible. Mettre à false pour masquer 
				'field'		=> false // Affiche les erreurs relative à chaque champs
			),
			'success'	=> array(
				'message'	=> __( 'Votre demande a bien été prise en compte et sera traitée dès que possible', 'tify' ),
				'display' 	=> false // Défaut false | Affichage du formulaire 'form' | Affichage du récapitulatif 'summary'
			),
			// Gestion des formulaires par étapes
			'steps' 	=> array(
				'count' 	=> 1,
				'buttons'	=> array(
					'backward' 	=> false,
					'forward'	=> false
				)
			),
			'anchor' 	=> "mkcf_container_". $form['ID'],
			'enctype'	=> false, // Attributs du formulaire en cas de présence de champs type fichier
			'summary'	=> false // Affiche un résumé des soummissions au formulaire avant le traitement définitif		
		);
		$this->current['options'] = $this->mkcf->functions->parse_options( $this->current['options'], $defaults );
		// Post traitement de la définition des options de formulaire
		$this->mkcf->callbacks->call( 'form_set_options', array( &$this->current['options'], $this->mkcf ) );
	 }
	
	/**
	 * Récupération de la liste complète des formulaires déclarés
	 * 
	 * @return array|null Tableau indexé de la liste complète des formulaires déclarés
	 */
	public function get_list(){
		return $this->forms;
	}
	
	/**
	 * Récupération d'un formulaire
	 * 
	 * @param int|object|null $form ID ou objet formulaire. null correspond au formulaire courant
	 * 
	 * @return array Un tableau dimensionné d'un formulaire et de ses attributs
	 */
	public function get( $form = null ){
		if( ! $form )
			return $this->get_current();
		elseif( is_array( $form ) && isset( $form['ID'] ) )
			return $this->forms[ $form['ID'] ];
		elseif( isset( $this->forms[$form] ) )
			return $this->forms[$form];
	}	
	
	/**
	 * Mise à jour des attributs d'un formulaire
	 * 
	 * @param int|object|null $form ID ou objet formulaire. null correspond au formulaire courant
	 */
	public function update( $form = null ){
		if( ! $form )
			$form = $this->get_current();

		if( isset( $form['ID'] ) )
			$this->forms[ $form['ID'] ] = $form;
	}	
	
	/**
	 * Récupération du formulaire courant
	 * 
	 * @return array|null Le formulaire déclaré courant
	 */
	public function get_current( ){
		return $this->current;
	}
	
	/**
	 * Récupération du formulaire courant
	 * 
	 * @return array|null Le formulaire déclaré courant
	 */
	public function is_current( $form ){
		if( ! $_form = $this->get( $form ) )
			return false;

		return $this->current['ID'] == $_form['ID'];
	}
	
	/**
	 * Définition du formulaire courant
	 * 
	 * @param int|object (requis) $form ID ou objet formulaire.
	 * 
	 * @return array Tableau dimensionné du formulaire courant
	 */
	public function set_current( $form ){
		if( ! $form )
			$this->reset_current();
		
		$_form = $this->get( $form );
		
		if( $_form['ID'] == $this->current['ID'] )
			return $this->current;
		else				
			return $this->current = $_form;
	}
		
	/**
	 * Réinitialisation du formulaire courant
	 */
	public function reset_current( ){
		$this->current = null;
	}
				
	/**
	 * ATTRIBUTS DE FORMULAIRE
	 */
	/**
	 * Récupération d'un attribut de formulaire
	 *
	 * @param string $attr Attribut du formulaire à récupérer. Par défaut, préfixe du formulaire 
	 * @param int|object|null $form ID ou objet formulaire. null correspond au formulaire courant	 
	 * 
	 * @return mixed La valeur de l'attribut requis pour un formulaire donné
	 */
	public function get_attr( $attr = 'ID', $form = null ){
		// Bypass
		if( ! $_form = $this->get( $form ) )
			return;
		
		if( isset( $_form[$attr] ) )
			return $_form[$attr];
	}
	
	/**
	 * Récupération de l'ID du formulaire
	 * 
	 * @param int|object|null $form ID ou objet formulaire. null correspond au formulaire courant
	 * 
	 * @return mixed La valeur du prefixe pour le formulaire requis 
	 */
	public function get_ID( $form = null ){
		return $this->get_attr( );
	}
	
	/**
	 * Récupération du prefixe du formulaire
	 * 
	 * @param int|object|null $form ID ou objet formulaire. null correspond au formulaire courant
	 * 
	 * @return mixed La valeur du prefixe pour le formulaire requis 
	 */
	public function get_prefix( $form = null ){
		return $this->get_attr( 'prefix', $form );
	}
	
	/**
	 * Récupération du titre du formulaire
	 * 
	 * @param int|object|null $form ID ou objet formulaire. null correspond au formulaire courant
	 * 
	 * @return mixed La valeur du titre pour le formulaire requis 
	 */
	public function get_title( $form = null ){
		return $this->get_attr( 'title', $form );
	}
	
	/**
	 * OPTIONS DE FORMULAIRE
	 */
	/**
	 * Récupération des options d'un formulaire
	 * 
	 * @param int|object|null $form ID ou objet formulaire. null correspond au formulaire courant
	 * 
	 * @return array Tableau dimensionné des options 
	 */
	public function get_options( $form = null ){
		// Bypass
		if( ! $_form = $this->get( $form ) )
			return;
		
		if( isset( $_form['options'] ) )
			return $_form['options'];
	}
	
	/**
	 * Récupération d'une option de formulaire
	 * 
	 * @param string $option (requis) Attribut de l'option requise 
	 * @param int|object|null $form ID ou objet formulaire. null correspond au formulaire courant
	 * 
	 * @return mixed Valeur de l'option requise 
	 */
	public function get_option( $option, $form = null ){
		// Bypass
		if( ! $options = $this->get_options( $form ) )
			return;
		
		if( isset( $options[ $option ] ) )
			return $options[ $option ];
	}
	
	/**
	 * Définition d'une option de formulaire
	 * 
	 * @param string $key (requis) Attribut de l'option
	 * @param string $value (requis) Valeur de l'option
	 * @param int|object|null $form ID ou objet formulaire. null correspond au formulaire courant
	 * 
	 * @return mixed Valeur de l'option requise 
	 */
	public function set_option( $key, $value, $form = null ){
		// Bypass
		if( ! $_form = $this->get( $form ) )
			return;	
		$this->forms[ $_form['ID'] ]['options'][$key] = $value;
		if( $this->current['ID'] == $_form['ID'] )
			$this->current['options'][$key] = $value; 
	}
	
	/**
	 * Définition de l'étape courante
	 *  
	 * @return int étape courante 
	 */	
	public function init_step( ){
		// Bypass
		if( ! $_form = $this->get_current( ) )
			return;
		
		if( isset( $_REQUEST[ 'step-'.$_form['prefix'].'-'.$_form['ID']] ) )
			$this->current['step'] = $this->mkcf->forms->set_step( $_REQUEST[ 'step-'.$_form['prefix'].'-'.$_form['ID']] );
		else
			$this->current['step'] = 1;
		
		$this->step_form_datas();					
				
		return $this->current['step'];
	}	
	
	/**
	 * Définition de l'étape courante
	 *  
	 * @return int étape courante 
	 */	
	public function set_step( $step ){
		// Bypass
		if( ! $this->get_current( ) )
			return;
		
		$this->current['step'] = $step;
		$this->step_form_datas();
		
		return $this->current['step'];
	}
	
	/**
	 * Récupération de l'étape courante
	 *  
	 * @return int étape courante 
	 */	
	public function get_step( ){
		// Bypass
		if( ! $this->get_current( ) )
			return;
		if( isset( $this->current['step'] ) )
			return $this->current['step'];
	}
	
	/**
	 * Passage à l'étape suivante
	 *  
	 * @return int étape courante 
	 */	
	public function next_step( ){
		// Bypass
		if( ! $this->get_current( ) )
			return;

		if( ! $steps = $this->get_option( 'steps' ) )
			return 0;
		
		++$this->current['step'];
		
		$this->step_form_datas();

		if( $this->current['step'] > $steps['count'] )
			return $this->current['step'] = 0;

		 return $this->current['step'];
	}
	
	/**
	 * 
	 */
	public function step_form_datas(){
		// Bypass
		if( ! $_form = $this->get_current( ) )
			return;
		
		$this->current['_fields'] = array();
		if( ! $this->get_option( 'steps' ) ) :
			$this->current['_fields'] = $_form['fields'];
		else :
			foreach( ( array ) $this->current['fields'] as $f ) :
				if( $f['step'] == $this->get_step( ) ) :
					array_push( $this->current['_fields'] , $f );
				endif;
			endforeach;
		endif;
	}
	
	/**
	 * Affichage d'un formulaire
	 *
	 * @param int|object $form ID ou objet formulaire. requis
	 * @param array $args Options d'affichage du formulaire
	 * 
	 * @return HTML Affiche ou retourne le formulaire requis
	 */
	public function display( $form, $echo = false ){
		// Bypass et Initialisation de l'élément courant
		if( ! $_form = $this->set_current( $form ) )
			return;

		// Initialisation des étapes de formulaire
		if( ! $this->get_step( ) )
			$this->init_step();		
			
		// Initialisation de l'enctype si le formulaire le nécessite
		$enctype = $this->get_option( 'enctype' ) ? "enctype=\"multipart/form-data\"" : false;			
		
		// Fonction de court-circuitage des attributs de formulaire post-affichage
		$this->mkcf->callbacks->call( 'form_before_display', array( &$_form ) );
		
		// Génération de la sortie HTML du formulaire
		$output = "";
		// Pré-affichage HTML
		$output .= $_form['before'];
		$this->mkcf->callbacks->call( 'form_before_output_display', array( &$output, $_form, $this->mkcf ) );		
		
		$output .= "\n<div id=\"mkcf_container_{$_form['ID']}\" class=\"".sprintf( $_form['container_class'], "mkcf_container" )."\">";
		// Message en cas de succès de soumission du formulaire
		$output .= "\n\t<div class=\"success notification\" style=\"display:". ( !empty( $_REQUEST[ 'mktzr_forms_results-'.$_form['ID'] ] )? 'inherit' : 'none' ) ."\">";
		$output .= "\n\t\t<p class=\"core_message\">";
		$success = $this->get_option( 'success' );
		$output .= ( ( $cache = $this->mkcf->handle->get_cache() ) && ! empty( $cache['success']['message'] ) ) ? $cache['success']['message'] : $success['message'];		 
		$output .= "\n\t\t<p>";
		$output .= "\n\t</div>";
		
		$output .= "\n\t<form method=\"{$_form['method']}\" id=\"mkcf_form_{$_form['ID']}\" class=\"".sprintf( $_form['form_class'], "mkcf_form" )."\" action=\"#". $this->get_option( 'anchor' ) ."\" {$enctype} style=\"display:". ( ( ! empty( $_REQUEST[ 'mktzr_forms_results-'.$_form['ID'] ] ) && ( $success['display'] != 'form' ) ) ? 'none' : 'inherit' ) ."\">";		
		
		// Champs cachés requis 
		$output .= $this->hidden_form_fields( $_form );
		
		// Affichage des erreurs
		$output .= "\n\t\t<div class=\"error notification\" style=\"display:".( ( $this->mkcf->errors->has() )? 'inherit' : 'none' )."\" >";
		$output .= $this->mkcf->errors->display();
		$output .= "\n\t\t</div>";
	
		// Affichage des champs de formulaire
		$output .= "\n\t\t<div class=\"fields-wrapper\">";
		$current_group = false;		
		foreach( (array) $this->mkcf->fields->get_fields_displayed() as $field ) :
			if( $field['group'] && $current_group != $field['group'] ) :
				if( $current_group )
					$output .= "\n\t\t\t</div>";
				$current_group = $field['group'];
				$output .= "\n\t\t\t<div class=\"fields-wrapper-group fields-wrapper-group-{$field['group']} fields-wrapper-order-{$field['order']}\">";
			endif;
			$output .= $this->mkcf->fields->display( $field, $_form['field_display_args'] );
		endforeach;
		if( $current_group )
			$output .= "\n\t\t\t</div>";
		$output .= "\n\t\t</div>";
		
		// Affichage des boutons
		$output .= "\n\t\t<div class=\"buttons-wrapper\">";
		$buttons = "";
		if( $this->get_option( 'steps' ) ) :
			$buttons .= "\n\t\t\t<div class=\"buttons-group step-buttons\">";
			if( $this->get_step( ) > 1 )
				$buttons .= "\n\t\t<button type=\"submit\" id=\"step_backward-{$_form['prefix']}-{$_form['ID']}\" class=\"backward\" name=\"submit-{$_form['prefix']}-{$_form['ID']}\" value=\"backward\">Précédent</button>";
			$buttons .= "\n\t\t\t</div>";
		endif;
		
		// Bouton de soumission
		if( $_form['options']['submit']['display'] ) :
			$buttons .= $_form['options']['submit']['before'];
			$buttons .= "\n\t\t\t<div class=\"buttons-group submit-button\">";
			$buttons .= $this->submit_button( $_form );
			$buttons .= "\n\t\t\t</div>";
			$buttons .= $_form['options']['submit']['after'];
		endif;
				
		// Court-circuitage de l'affichage des boutons
		$this->mkcf->callbacks->call( 'form_buttons_display', array( &$buttons, $this->mkcf ) );
		
		$output .= $buttons;		
		$output .= "\n\t\t</div>";
		
		$output .= "\n\t</form>";
		$output .= "\n</div>";
		
		// Post-affichage HTML
		$output .= $_form['after'];
		$this->mkcf->callbacks->call( 'form_after_output_display', array( &$output, $_form, $this->mkcf ) );	
		
		// Fonction de court-circuitage de l'affichage du formulaire
		$this->mkcf->callbacks->call( 'form_output_display', array( &$output, $_form, $this->mkcf ) );
		
		// Réinitialisation de l'élément courant
		$this->reset_current();
		
		if( $echo )
			echo $output;
		else 
			return $output;	
	}

	/**
	 * Champs cachés de soumission de formulaire
	 */
	function hidden_form_fields( $form = null ){
		if( ! $_form = $this->get( $form ) )
			return;
		// Definition de l'identifiant de formulaire
		$slug = $_form['prefix']."-".$_form['ID'];	

		$output  = "";
		$output .= "\n\t\t<input type=\"hidden\" name=\"{$_form['prefix']}-form_id\" value=\"". esc_attr( $_form['ID'] ) ."\">";		
		$output .= wp_nonce_field( 'submit_'.$slug, '_'.$_form['prefix'].'_nonce', true, false );
		if( $this->is_current( $_form ) ) :			
			$output .= "\n\t\t<input type=\"hidden\" name=\"step-$slug\" value=\"". esc_attr( $this->get_step( ) ) ."\">";
			$output .= "\n\t\t<input type=\"hidden\" name=\"transport-$slug\" value=\"". esc_attr( $this->mkcf->handle->get_transport() ) ."\">";
			$output .= "\n\t\t<input type=\"hidden\" name=\"session-$slug\" value=\"". esc_attr( $this->mkcf->handle->get_session() ) ."\">";
		endif;
		
		return $output;
	}
	
	/**
	 * Bouton de soumission
	 */
	function submit_button( $form = null ){
		if( ! $_form = $this->get( $form ) )
			return;
		
		$class = ! empty( $_form['options']['submit']['class'] ) ? "submit {$_form['prefix']}-submit ".$_form['options']['submit']['class'] : "submit {$_form['prefix']}-submit";
		
		$output  = "";
		$output .= "<button type=\"submit\" id=\"submit-{$_form['prefix']}-{$_form['ID']}\" class=\"$class\" name=\"submit-{$_form['prefix']}-{$_form['ID']}\" value=\"submit\">";
		$output .= $_form['options']['submit']['label'];
		$output .= "</button>";
		
		return $output;
	}
}