<?php
/**
 * Méthodes de traitement des formulaires
 */
class MKCF_Handle{
	var	$handle_redirect, 	// Url de redirection appliquée après le traitement
		$transport, 		// Données de formulaire embarquées
		$original_request, 	// Requête d'origine
		$parsed_request, 	// Requête translatée
		$cache_expired;		// Délai d'expiration du cache MINUTE_IN_SECONDS | HOUR_IN_SECONDS | DAY_IN_SECONDS | WEEK_IN_SECONDS | YEAR_IN_SECONDS
	
	public function __construct(MKCF $master) {
        $this->mkcf = $master;
    }
	
	/**
	 * Lancement de la tâche de traitement des formulaires
	 */
	public function proceed(){
		$this->cache_expired = HOUR_IN_SECONDS;
		foreach( (array) $this->mkcf->forms->get_list() as $form ) :
			$this->mkcf->forms->set_current( $form );
			$this->handle();
		endforeach;			
	}
	
	/* = REQUÊTE = */
	/** == Récupération de la méthode de récupération de la soumission de formulaire
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant
	 * @return 
	 == **/
	 public function get_method( $form = null ){	 	
	 	// Bypass
	 	if( ! $_form = $this->mkcf->forms->get( $form ) )
			return;

		switch( $_form['method'] ) :
			case 'post' :
				$method = $_POST;
			break;
			case 'get' :
				$method = $_GET;
			break;
			default :
			case 'request' :
				$method = $_REQUEST;
			break;
		endswitch;
		
		return $method;
	 }
	
	/** == Vérification de la soumission d'une requête pour un champs  
	 * @param array $field (requis) Tableau dimensionné d'un champ
	 * @return mixed|null Valeur de la requête
	 == **/
	 public function is_request( $field ){
	 	// Bypass
	 	if( ! $_method = $this->get_method( $field['form_id'] ) )
			return;
		
		return isset( $_method[ $field['form_prefix'] ][ $field['form_id'] ][ $field['slug'] ] );			
	 }
	
	/** == Récupération de la valeur de requête pour un champs  
	 * @param array $field (requis) Tableau dimensionné d'un champ
	 * @return mixed|null Valeur de la requête
	 == **/
	 public function get_request( $field ){
	 	// Bypass
	 	if( ! $_method = $this->get_method( $field['form_id'] ) )
			return;
		
		$request = false;
		
		if( isset( $_method[ $field['form_prefix'] ][ $field['form_id'] ][ $field['slug'] ] ) )
			$request = $_method[ $field['form_prefix'] ][ $field['form_id'] ][ $field['slug'] ];	
		
		$this->mkcf->callbacks->call( 'handle_get_request', array( &$request, $field, $_method, $this->mkcf ) );
		
		return $request;		
	 }	 
	
	/* = SESSION DE SOUMISSION DE FORMULAIRE = */	
	/** == Initialisation de la session == **/
	public function init_session(){
		// Bypass
		if( ! $_form = $this->mkcf->forms->get_current( ) )
			return;
		
		$this->mkcf->forms->forms[ $_form['ID'] ]['session'] = wp_hash( uniqid().$_form['prefix'].$_form['ID'] );
		$this->init_cache( $this->mkcf->forms->forms[ $_form['ID'] ]['session'] );
		
		return $this->mkcf->forms->forms[ $_form['ID'] ]['session'];
	}
	
	/** == Récupération l'identifiant de session == 
	 * @return (string) identifiant unique de session
	 */
	public function get_session(){		
		if( ! $_form = $this->mkcf->forms->get_current( ) )
			return;
		if( ! empty( $this->mkcf->forms->forms[ $_form['ID'] ]['session'] ) )
			return $this->mkcf->forms->forms[ $_form['ID'] ]['session'];
		elseif( ! empty( $_REQUEST[ 'mktzr_forms_results-'.$_form['ID'] ] ) )
			return $this->mkcf->forms->forms[ $_form['ID'] ]['session'] = $_REQUEST[ 'mktzr_forms_results-'.$_form['ID'] ];
		elseif( ! empty( $this->original_request[ 'session-'.$_form['prefix'].'-'.$_form['ID']] ) )
			return $this->mkcf->forms->forms[ $_form['ID'] ]['session'] = $this->original_request[ 'session-'.$_form['prefix'].'-'.$_form['ID']];
		
		return null;
	}
		
	/* = MISE EN CACHE = */
	/** == Initialisation du cache == **/
	public function init_cache( $session ){
		return set_transient( 'mkcf_cache_'. $session, array( 'session' => $session ), $this->cache_expired );
	}
	/** == Définition du cache ==  **/
	public function set_cache( $args = array() ){
		// Bypass
		if( ! $session = $this->get_session( ) )
			return;
		
		if( ! $defaults = get_transient( 'mkcf_cache_'. $session ) )
			$defaults = $this->mkcf->forms->get_options( );

		$args = wp_parse_args( $args, $defaults );
		
		return set_transient( 'mkcf_cache_'. $session, $args, $this->cache_expired );
	}	
	/** == Récupération du cache ==  **/
	public function get_cache( $session = null ){
		if( ! $session )
			$session = $this->get_session( );
		
		if( ! $session )
			return false;
		
		return $this->mkcf->functions->parse_options( get_transient( 'mkcf_cache_'. $session ), $this->mkcf->forms->get_options( ) );
	}	
	/** == Vérification du cache ==  **/
	public function has_cache( $session = null ){
		if( ! $session )
			$session = $this->get_session( );
		
		if( ! $session )
			return false;
		
		return ! ( false === get_transient( 'mkcf_cache_'. $session ) );
	}
	/** == Nettoyage du cache ==  **/
	public function purge_cache(){
		return tify_purge_transient( 'mkcf_cache_' );
	}	
	
	/* = DONNÉES EMBARQUÉES = */
	/**
	 * Initialisation du traitement des données embarquées
	 */
	 public function init_transport( ){	 	
	 	// Bypass
	 	if( ! $_form = $this->mkcf->forms->get_current() )
			return;
	 	
		// Récupération des données embarquées portées par le formulaire
	 	if( ( $request = $this->get_method() ) && isset( $request[ 'transport-'.$_form['prefix'].'-'.$_form['ID']] ) )
			return $this->transport = $this->decode_transport( $request[ 'transport-'.$_form['prefix'].'-'.$_form['ID']] );		
		
		return $this->transport = array();	
	 }	
		
	/**
	 * Récupération des données embarquées
	 */
	public function get_transport( ){		
		return base64_encode( serialize( $this->transport ) );		
	}	
	
	/**
	 * Décodage de la chaîne de transport
	 */
	public function decode_transport( $transport ){
		return unserialize( ( base64_decode( $transport ) ) );		
	}	
	
	/**
 	* Traitement du formulaire
 	*/
	private function handle(){	
		// Récupération du formulaire courant
		$_form = $this->mkcf->forms->get_current();		
		
		// Initialisation de l'étape
		$this->mkcf->forms->init_step();
		
		// Initialisation des données embarquées
		$this->init_transport( );

		// Nettoyage du cache
		$this->purge_cache();
		// Vérification de session
		if( ( $session = $this->get_session() ) && ( ! $this->has_cache( $session ) ) )
			wp_die( __( '<h2>Erreur lors de la soumission du formulaire</h2><p>Votre session de soumission de formulaire est invalide ou arrivée à expiration</p>', 'tify' ) );
				
		// Bypass
		if( ! $this->original_request = $this->get_method() )
			return;		
		if( ! isset( $this->original_request['_'.$_form['prefix'].'_nonce'] ) )
			return;
		
		// Vérification de sécurité
		/// Provenance de la soumission du formulaire	
		if( ! wp_verify_nonce( $this->original_request['_'.$_form['prefix'].'_nonce'], 'submit_'.$_form['prefix'].'-'.$_form['ID'] ) ) 
			wp_die( __( '<h2>Erreur lors de la vérification d\'origine de la soumission de formulaire</h2><p>Impossible de déterminer l\'origine de la soumission de votre formulaire.</p>', 'tify' ), array( 'form_id' => $_form['ID'] ) );
		/// Valeur de l'id du formulaire	
		if( empty( $this->original_request[ $_form['prefix'].'-form_id'] ) )
			wp_die( __( '<h2>Erreur lors de la vérification d\'origine de la soumission de formulaire</h2><p>Impossible de définir l\'ID votre formulaire.<p>', 'tify' ) );
		/// Session
		if( ! $session )
			$session = $this->init_session();
		// Mise à jour du cache
		$this->set_cache();
		
		// Traitement de la requête
		if( ! $this->parse_request( $this->original_request ) )
			return;
		
		// Vérification des champs de formulaire
		$this->check_request();

		// Affichage du formulaire et des erreurs	
		if( $this->mkcf->errors->has() )
			return;
		
		// Affichage du formulaire pour l'étape suivante
		if( $this->mkcf->forms->next_step( ) )
			return;
		
		// Post traitement avant la redirection
		$this->mkcf->callbacks->call( 'handle_before_redirect', array( &$this->parsed_request, $this->original_request, $this->mkcf ) );
		
		// Affichage du formulaire et des erreurs post vérification	
		if( $this->mkcf->errors->has() )
			return;		
		
		// Redirection après le traitement
		// IMPORTANT : doit contenir l'identifiant de session du formulaire			
		$location = ( ! empty( $this->original_request['_wp_http_referer'] ) ) 
			? add_query_arg( "mktzr_forms_results-{$_form['ID']}", $this->get_session(), $this->original_request['_wp_http_referer'] ) 
			: add_query_arg( "mktzr_forms_results-{$_form['ID']}", $this->get_session(), home_url('/') );	
		
		$this->mkcf->callbacks->call( 'handle_redirect', array( &$location, $this->mkcf ) );
	
		if( $location ) :
			$this->reset_request();
			wp_redirect( $location );
			exit;
		endif;
	}
	
	/**
	 * Traitement des élément de requête
	 * 
	 * @param array $request (requis) Tableau dimensionné de la requête à traité. $_POST|$_GET|$_REQUEST
	 * @param int|object|null $form ID ou objet formulaire. null correspont au formulaire courant
	 */
	public function parse_request( $request, $form = null  ){		
		// Bypass
	 	if( ! $_form = $this->mkcf->forms->get_current() )
			return;
		
		$this->parsed_request['session'] 	= $this->get_session();
		$this->parsed_request['form_id'] 	= $_form['ID'];
		$this->parsed_request['prefix'] 	= $_form['prefix'];
		$this->parsed_request['step'] 		= $request[ 'step-'.$_form['prefix'].'-'.$_form['ID']];
		$this->parsed_request['transport'] 	= $request[ 'transport-'.$_form['prefix'].'-'.$_form['ID']];
		$this->parsed_request['submit'] 	= $request[ 'submit-'.$_form['prefix'].'-'.$_form['ID']];		
		$this->parsed_request['fields'] 	= array();

		// Traitement des données embarquées
		foreach( $this->transport as $tk => $tv )
			$this->parsed_request['fields'][$tk] = $tv;
		// Traitement du bouton de soumission
		if( ! $this->parse_submit() )
			return;		
		// Traitement des champs de formulaire
		foreach( $this->mkcf->fields->get_fields_displayed() as $field ) :				
			// Bypass des champs qui ne sont pas à traiter dans la requête		
			if( ! $this->mkcf->fields->types->type_supports( 'request', $field['type'] ) )
				continue;
			// Conservation de l'arborescence des champs
			$this->parsed_request['fields'][$field['slug']] = $field;
			// Conservation de la valeur originale attribué au champ
			$this->parsed_request['fields'][$field['slug']]['original_value'] = $field['value'];
			// Attribution de la nouvelle valeur du champ passée par la requête de soumission
			$this->parsed_request['fields'][$field['slug']]['value'] = ( ! $value = $this->get_request( $field ) )? $field['value'] : ( ( is_string( $value ) ) ? stripslashes( $value ): $value );
			// Traitement des valeurs par type de champs
			switch( $field['type'] ) :
				case 'textarea' : 
					$this->parsed_request['fields'][$field['slug']]['value'] = nl2br( $this->parsed_request['fields'][$field['slug']]['value'] );
					break;
				default :
					$this->mkcf->callbacks->call( 'handle_parse_request', array( &$this->parsed_request['fields'][ $field['slug'] ], $this->mkcf ) );
					break;
			endswitch;	
			$this->parsed_request['values'][$field['slug']] = is_array( $this->parsed_request['fields'][$field['slug']]['value'] ) ? array_map( 'esc_attr', $this->parsed_request['fields'][$field['slug']]['value'] ) : esc_attr( $this->parsed_request['fields'][$field['slug']]['value'] );		
		endforeach;
		// Définition des données embarquées
		$this->transport = $this->parsed_request['fields'];
		$this->parsed_request['transport'] = $this->get_transport();
	
		return true;
	}	
	
	/**
	 * Vérification du bouton de soumission du formulaire
	 */
	public function parse_submit(){
		// Gestion des étapes de formulaires
		if( $this->parsed_request['submit'] == 'backward' ) :
			$this->mkcf->forms->set_step( --$this->parsed_request['step'] );
			return false;
		else :
			$this->mkcf->forms->set_step( $this->parsed_request['step'] );
		endif;
		
		// Callback
		$continue = true;
		if( ! $this->mkcf->callbacks->call( 'handle_parse_submit', array( &$continue, $this->parsed_request['submit'], $this->mkcf ) ) )
			return $continue;				
		
		return true;
	}	
	
	/**
	 * Vérification (Tests d'intégrité) des variables de saisie du formulaire.
	 * 
	 * @param array $request Tableau dimensionné de la requête à traité. Par défaut |$_POST|$_GET|$_REQUEST
	 */ 
	public function check_request( $request = array() ){	
		if( ! $request )
			$request = $this->parsed_request;
		// Bypass
		if( !isset( $request['form_id'] ) )
			return;
		if( ! $_form = $this->mkcf->forms->set_current( $request['form_id'] ) )
			return;

		foreach( $this->mkcf->fields->get_fields_displayed() as $field ) :
			$errors = array();
			// Bypass : Le champ n'est pas présent dans la soumission de formulaire
			if( ! isset( $this->original_request[ $_form['prefix'] ][ $_form['ID'] ][ $field['slug'] ] ) && ! $field['required'] ) 
				continue;
			// Champs requis	
			if( $field['required'] && empty( $request['fields'][ $field['slug'] ]['value'] ) ) :
				if( is_bool( $field['required'] ) ) :
					$errors[ 'required:'. $field['slug'] ] = sprintf( __( 'Le champ "%s" ne peut être vide', 'tify' ) , $field['label'] );
				elseif( is_string( $field['required'] ) ) :
					$errors[ 'required:'. $field['slug'] ] = sprintf( $field['required'], $field['label'] );
				endif;
				$this->mkcf->callbacks->call( 'handle_check_required', array( &$errors, $request['fields'][ $field['slug'] ], $this->mkcf ) );
			// Tests d'integrité
			else :			
				if( $field['integrity_cb'] ) :
					$this->mkcf->integrity->check( $field['integrity_cb'], $request['fields'][ $field['slug'] ]['value'] );
					if( isset( $this->mkcf->integrity->errors ) ) :						
						foreach( $this->mkcf->integrity->errors as $error ) :
							if( ! $error ) continue;
							$errors[] = sprintf( $error , $field['label'] );						
						endforeach;
						$this->mkcf->integrity->errors = array();
					endif;
				endif;
				
				// Post traitement
				$this->mkcf->callbacks->call( 'handle_check_request', array( &$errors, $request['fields'][ $field['slug'] ], $this->mkcf ) );
			endif;
			// Implémentation des erreurs
			if( ! empty( $errors ) )
				$this->mkcf->errors->field_set( $errors, $field );			
		endforeach;
	}
		
	/**
	 * Suppression des données de requête
	 */
	public function reset_request(){
		$this->original_request = null;
		$this->parsed_request = null;
	}
}