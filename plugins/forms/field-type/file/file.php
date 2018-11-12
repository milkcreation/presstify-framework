<?php
class tify_forms_field_file{
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
		$this->mkcf->callbacks->field_type_set( 'form_set_options', 'file', array( $this, 'cb_form_set_options' ) );
		$this->mkcf->callbacks->field_type_set( 'field_type_output_display', 'file', array( $this, 'cb_field_type_output_display' ) );				
		$this->mkcf->callbacks->field_type_set( 'handle_check_request', 'file', array( $this, 'cb_handle_check_request' ) );
		$this->mkcf->callbacks->field_type_set( 'handle_get_request', 'file', array( $this, 'cb_handle_get_request' ) );
		$this->mkcf->callbacks->field_type_set( 'handle_before_redirect', 'file', array( $this, 'cb_handle_before_redirect' ) );
	}
		
	/**
	 * Déclaration du type de champ
	 */
	function set_type(){		
		$this->mkcf->fields->set_type(
			array(
				'slug'			=> 'file',
				'label'			=> __( 'File upload', 'mktzr_forms' ),
				'section' 		=> 'input-fields',
				'order' 		=> 1,
				'supports'		=> array( 'label', 'placeholder', 'integrity-check', 'request' ),
				'options'		=> array(
					'allowed_file_types' 	=> false,
					'conservation'			=> false
				)				
			)
		);
	}
	
	/**
	 * MCF CALLBACKS
	 */
	/**
	 * Définition des options de formulaire
	 */
	function cb_form_set_options( &$options ){
		$options['enctype'] = true;
	}	
	
	/**
	 * Affichage du champ
	 */
	function cb_field_type_output_display( &$output, $field ){
		// Bypass
		if( $field['type'] != 'file' )
			return;			
			
		$output .= "\n\t<input type=\"file\" ";
	
		$field_class = rtrim( trim( sprintf( $field['field_class'], "field field-{$field['form_id']} field-{$field['slug']}") ) );
		$output .= " name=\"". esc_attr( $this->mkcf->fields->get_name( $field ) ) ."\" id=\"field-{$field['form_id']}-{$field['slug']}\" class=\"".$field_class."\"";			
		$output .= "/>";
		/// Transport des données fichier
		$output .= "\n\t<input type=\"hidden\" value=\"". esc_attr( $field['value'] ) ."\" name=\"{$field['form_prefix']}[{$field['form_id']}][{$field['slug']}]\" />";
		
		// Conservation des données
		/// Bypass
		if( ! $field['options']['conservation'] )
			return;		
		/// Affichage du nom de fichier
		$output .= "\n\t<input type=\"text\" value=\"". ( ( $file = unserialize( @ base64_decode( $field['value'] ) ) ) ? esc_attr( $file['name'] ) : false ) ."\" placeholder=\"".$field['placeholder']."\" readonly=\"readonly\" autocomplete=\"off\"/>";
	}
	
	/**
	 * Récupération de la valeur du champ
	 */
	function cb_handle_get_request( &$request, $field, $_method ){
		// Bypass
		if( $field['type'] != 'file' )
			return;

		if( $file = $this->parse_file_request( $field ) ) :
			if( $field['options']['conservation'] ) :
				// Déplacement du fichier dans le repertoire de stockage temporaire (pour les fichiers autorisés uniquement)
				if( $this->check_file_type_is_allowed( $file, $field ) ) :					
					$filename 		= $file['tmp_name'];		
					$destination 	= $this->mkcf->dirs->dirname( 'temp' ) ."/". basename( $file['tmp_name'] );						
					if( ! move_uploaded_file( $filename, $destination ) )
						wp_die( sprintf( __( '<h1>ERREUR SYSTEME</h1><p>Impossible de déplacer le fichier du champs "%s" dans le repertoire de stockage temporaire.</p>', 'mktzr_forms' ), $field['label'] ) );
					$file['tmp_name'] = $destination;
				endif;
			endif;
			
			$request = @ base64_encode( serialize( $file ) );
		elseif( ! $field['options']['conservation'] ) :
			$request = false;
		endif;
	}
	
	/**
	 * Vérification des requêtes
	 */
	function cb_handle_check_request( &$errors, $field ){
		// Bypass
		if( $field['type'] != 'file' )
			return;
		if( ! $file = unserialize( @ base64_decode( $field['value'] ) ) ) 
			return;		
		
		// Retour des erreurs PHP
		if( $file['error'] > 0 ):
			switch ( $file['error'] ) :
				case 1:
				case 2:
					$errors[] = sprintf( __( 'La taille du fichier téléchargé excède la valeur autorisée pour le champ "%s".', 'mktzr_forms' ), $field['label'] );
					break;	
				case 3:
					$errors[] = sprintf( __( 'ERREUR SYSTÈME : Le fichier du champs "%s" n\'a été que partiellement téléchargé.', 'mktzr_forms' ), $field['label'] );
					break;		
				case 4:
					if( $field['required'] )
						$errors[] = sprintf( __( 'Aucun fichier n\'a été téléchargé dans le champs "%s".', 'mktzr_forms' ), $field['label'] );
					break;
				case 6:
					$errors[] = __( 'ERREUR SYSTÈME : Le dossier temporaire est manquant', 'mktzr_forms' );
					break;
				case 7:
					$errors[] = __( 'ERREUR SYSTÈME : Échec de l\'écriture du fichier sur le disque.', 'mktzr_forms' );
					break;
				case 8:
					$errors[] = __( 'ERREUR SYSTÈME : Une extension PHP a arrêté l\'envoi de fichier.', 'mktzr_forms' );
					break;
			endswitch;
		// Test des droits d'extension de fichier
		elseif( ! $this->check_file_type_is_allowed( $file, $field ) ) :
			$errors[] = sprintf( __( 'Type de fichier non autorisé dans le champ "%s".', 'mktzr_forms' ), $field['label'] );		
		endif;
	}
	
	/**
	 * 
	 */
	function cb_handle_before_redirect( &$parsed_request, $original_request, $mkcf ){
		$sanitized = array( );
		// Déplacement des fichiers du répertoire de stokage temporaire vers le repertoire de stockage définitif
		foreach( $parsed_request['fields'] as $slug => &$field ) :
			// Bypass
			if( $field['type'] != 'file' ) 
				continue;				
			if( ! $file = unserialize( @ base64_decode( $field['value'] ) ) ) 
				continue;
		
			$filename 		= $file['tmp_name'];					
			$destination 	= $this->mkcf->dirs->dirname( 'upload' ) ."/". wp_unique_filename( $this->mkcf->dirs->dirname( 'upload' ), $file['name'] );
									
			if( ! @ copy( $filename, $destination ) )
				wp_die( sprintf( __( '<h1>ERREUR SYSTEME</h1><p>Impossible de déplacer le fichier du champ "%s".</p>', 'mktzr_forms' ), $field['label'] ) );
			
			$sanitized[$slug] 	= $field;
			$file['name'] 		= basename( $destination );
			$field['value']		= @ base64_encode( serialize( $file ) );
		endforeach;
		
		// Nettoyage du dossier temporaire
		foreach( $sanitized as $slug => $field ) :
			// Bypass
			if( $field['type'] != 'file' ) 
				continue;				
			if( ! $file = unserialize( @ base64_decode( $field['value'] ) ) ) 
				continue;
			@ unlink( $file['tmp_name'] );
		endforeach;
	}
	
	/**
	 * CONTROLEURS
	 */
	/**
	 * Type d'extension par défaut
	 */
	function check_file_type_is_allowed( $file, $field ){
		$ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
		
		$allowed_file_types = array();
		if( ! $field['options']['allowed_file_types'] )
			foreach( array_keys( get_allowed_mime_types() ) as $exts )
				foreach( explode( '|', $exts ) as $ext )
					array_push( $allowed_file_types, $ext );
		elseif( is_string( $field['options']['allowed_file_types'] ) )
			$allowed_file_types = explode( ' ', $field['options']['allowed_file_types'] );
		else
			$allowed_file_types = $field['options']['allowed_file_types'];		

		return in_array( $ext, $allowed_file_types );
	}
	
	/**
	 * Traitement de la requête $_FILES relative au champs
	 */
	function parse_file_request( $field ){
		$form_id = $field['form_id'];
		$form_prefix =  $this->mkcf->forms->get_prefix( $form_id );

		// Bypass
		if( ! isset( $_FILES[ $form_prefix ] ) )
			return;
			
		foreach( array( 'name', 'type', 'tmp_name', 'error', 'size' ) as $index )
			$_file[$index] = $_FILES[ $form_prefix ][$index][$form_id][$field['slug']];
		
		if( empty( $_file['name'] ) || empty( $_file['type'] ) || empty( $_file['tmp_name'] ) || empty( $_file['size'] ) )
			return;		
		
		return $_file;
	}
}