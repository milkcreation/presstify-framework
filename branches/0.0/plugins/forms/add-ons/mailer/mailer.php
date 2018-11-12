<?php
/**
 * OPTIONS DE L'ADDON
	array(
 		'debug' 				=> false, // true : Envoi de l'email | false : force l'affichage de l'email plutôt que l'envoi
		'notification' 			=> array( // Envoi un email de notification aux administrateurs du site
 			'from' 				=> (string|array) // Email de l'expediteur
 				array( 
  					'name' 	=> [value] // Nom de l'expediteur (optionnel) | défaut : Nom du blog
  					'email' => [value] // Doit être une adresse email valide | défaut : Adresse de l'administrateur du site
  				)
  			),
  			'to' 				=> // (string|array|array_multi) Email(s) de destinataire(s) | Peut être la valeur d'un champs du formulaire %%[field_slug]%% à condition qu'il s'agissent d'une adresse email
	 			array(
	 				array( 
	  					'name' 	=> [value] // Nom du destinataire1 (optionnel) | défaut : Nom du blog
	  					'email' => [value] // Adresse email du destinataire1 | défaut : Adresse de l'administrateur du site
	 				),
	 				...
	 			)
  			),
			'cc' 				=> // (string|array|array_multi) Email(s) de destinataire(s) en copie (optionnel)
	 			array(	
	 				array( 
	  					'name' 	=> [value] // Nom du destinataire1 en copie (optionnel)
	  					'email' => [value] // Adresse email du destinataire1 en copie
	 				)
					...
				)
  			),
 			'bcc' 				=> // Email(s) de destinataire(s) en copie cachée
 				array(
	 				array( 
	  					'name' 	=> [value] // Nom du destinataire1 en copie cachée
	  					'email' => [value] // Adresse email du destinataire1 en copie cachée
	 				)
					...
				)
  			),  			
  			'subject'			=> [value], // Peut être la valeur d'un champs du formulaire %%[field_slug]%%
			'header_message'	=> [value], // Ajout d'une entête de mail (optionnel)
			'message'			=> [value], // Personnalisation du message avec les variable de formulaire (optionnel)
			'footer_message'	=> [value] // Ajout d'un pied de page de mail (optionnel)
  		), 
		'confirmation' 	=> false array( // Envoi un email de confirmation à l'expediteur du message
  			'from' 				=> (string|array) // Email de l'expediteur
 				array( 
  					'name' 	=> [value] // Nom de l'expediteur (optionnel) | défaut : Nom du blog
  					'email' => [value] // Doit être une adresse email valide | défaut : Adresse de l'administrateur du site
  				)
  			),
  			'to' 				=> // (string|array|array_multi) Email(s) de destinataire(s) | Peut être la valeur d'un champs du formulaire %%[field_slug]%% à condition qu'il s'agissent d'une adresse email
 				array(
	 				array( 
	  					'name' 	=> [value] // Nom du destinataire1 (optionnel) | défaut : Nom du blog
	  					'email' => [value] // Adresse email du destinataire1 | défaut : Adresse de l'administrateur du site
	 				),
	 				...
	 			)
  			),
			'cc' 				=> // (string|array|array_multi) Email(s) de destinataire(s) en copie (optionnel)
 				array(	
	 				array( 
	  					'name' 	=> [value] // Nom du destinataire1 en copie (optionnel)
	  					'email' => [value] // Adresse email du destinataire1 en copie
	 				)
					...
				)
  			),
 			'bcc' 				=> // Email(s) de destinataire(s) en copie cachée
 				array(
	 				array( 
	  					'name' 	=> [value] // Nom du destinataire1 en copie cachée
	  					'email' => [value] // Adresse email du destinataire1 en copie cachée
	 				)
					...
				)
  			),  			
  			'subject'			=> [value], // Peut être la valeur d'un champs du formulaire %%[field_slug]%%
			'header_message'	=> [value], // Ajout d'une entête de mail (optionnel)
			'message'			=> [value], // Personnalisation du message avec les variable de formulaire (optionnel)
			'footer_message'	=> [value] // Ajout d'un pied de page de mail (optionnel)
  		)
 */
/**
 * 
 */	
class tify_forms_addon_mailer{
	private $mkcf, $tiFy;
	
	/**
	 * Initialisation
	 */
	function __construct( MKCF $mkcf ){
		// TiFY
		global $tiFy;		
		$this->tiFy 	= $tiFy;
		
		// MKCF	
		$this->mkcf 	= $mkcf;
		
		// Définition des options par défaut
		$this->set_default_form_options();
		$this->set_default_field_options();
		
		// Actions et Filtres Wordpress
		// ...
		
		// Callbacks
		$this->mkcf->callbacks->addons_set( 'handle_before_redirect', 'mailer', array( $this, 'cb_handle_before_redirect' ) );
	}
	
	/**
	 * Définition des options par défaut pour les formulaire
	 */
	function set_default_form_options(){
		$this->mkcf->addons->set_default_form_options( 'mailer',
			array(
				'debug' 		=> false,		
				'notification' 	=> array(
					'send' 			=> true,
					'from' 			=> array( 
						'name' 			=> get_bloginfo( 'name' ),
						'email' 		=> get_option( 'admin_email' )
					),
					'to'	=>  array( 
						array(
							'name' 			=> get_bloginfo( 'name' ),
							'email' 		=> get_option( 'admin_email' )
						)
					),
					'replyto'		=>  array( 
						'name' 			=> get_bloginfo( 'name' ),
						'email' 		=> get_option( 'admin_email' )
					),
					'cc' 			=> false,
					'bcc' 			=> false,
					'subject'		=> sprintf( __( 'Vous avez une nouvelle demande de contact sur le site %s', 'mktzr_forms'), get_bloginfo('name') ),
					'header'		=> '',
					'message'		=> '',
					'footer'		=> ''	
				),
				'confirmation' 	=> array(
					'send' 			=> false,
					'from' 			=> array( 
						'name' 			=> get_bloginfo('name'),
						'email' 		=> get_option( 'admin_email' )
					),
					'to'		=>  array( 
						array(
							'name' 			=> get_bloginfo( 'name' ),
							'email' 		=> get_option( 'admin_email' )
						)
					),
					'replyto'	=>  array( 
						'name' 		=> get_bloginfo('name'),
						'email' 	=> get_option( 'admin_email' )
					),
					'cc' 		=> false,
					'bcc' 		=> false,
					'subject'	=> sprintf( __( 'Votre demande de contact sur le site %s', 'mktzr_forms'), get_bloginfo('name') ),
					'header'	=> '',
					'message'	=> '',
					'footer'	=> ''	
				)
			)
		);			
	}	
	
	/**
	 * Définition des options par défaut pour les champs de formulaire
	 */
	function set_default_field_options(){
		$this->mkcf->addons->set_default_field_options( 'mailer', 
			array( 
				'ignore' 		=> false 		// Permet d'ignorer l'affichage du champ dans l'envoi de mail
			) 
		);
	}
	
	/**
	 * CALLBACKS
	 */
	/**
	 * Envoi de l'email
	 */
	function cb_handle_before_redirect( $parsed_request, $original_request ){
		// Récupération du controleur de mail
		tify_require( 'mailer' );
		
		// Envoi du message de notification
		if( ( $options = $this->mkcf->addons->get_form_option( 'notification', 'mailer' ) ) && $options['send'] ) :	
			// Préparation du mail
			$options = $this->parse_options( $options, $parsed_request['fields'] );
			$tiFy_Mailer = new tiFy_Mailer;
			$tiFy_Mailer->prepare( $options );
			
			
			if( $this->mkcf->addons->get_form_option( 'debug', 'mailer' ) )
				$tiFy_Mailer->debug_email();
			else
				$tiFy_Mailer->send();		
		endif; 	
	
		//Envoi du message de confirmation
		if( ( $options = $this->mkcf->addons->get_form_option( 'confirmation', 'mailer' ) ) && $options['send'] ) :	
			// Préparation du mail
			$options = $this->parse_options( $options, $parsed_request['fields'] );
			$tiFy_Mailer = new tiFy_Mailer;
			$tiFy_Mailer->prepare( $options );
			
			if( $this->mkcf->addons->get_form_option( 'debug', 'mailer' ) )
				$tiFy_Mailer->debug_email();
			else
				$tiFy_Mailer->send();		
		endif;
	}
	
	/**
	 * Traitement des options
	 */
	function parse_options( $options, $fields ){
		// Expéditeur
		$from = $this->parse_contact( $options['from'], $fields );
		// Destinataire
		$to = $this->parse_contact( $options['to'], $fields );
		// Destinataire en copie
		$cc = $this->parse_contact( $options['cc'], $fields );
		// Destinataire en copie cachée
		$bcc = $this->parse_contact( $options['bcc'], $fields );
		// Destinataire en copie cachée
		$replyto = $this->parse_contact( $options['replyto'], $fields );
		// Sujet
		$subject = $this->mkcf->functions->translate_field_value( $options['subject'], $fields, $options['subject'] );		
		// Attachments
		$attachments = array();

		foreach( $fields as $field ) :
			if( $field['type'] != 'file' ) continue;
			if( ! $file = unserialize( @ base64_decode( $field['value'] ) ) ) 
				continue;
			array_push( $attachments, WP_CONTENT_DIR. "/uploads/mktzr_forms/upload/". $file['name'] );
		endforeach;		
		// Message
		/** @todo Format Raw : $message = mktzr_forms_email_raw_notification( $form_id, $subject, $this->mkcf->_submit['request'] ); **/	
		$message = "";
		if( $header = $options['header'] )
			$message .= $header;
		$message .= $this->email_html( $subject, $fields );
		if( $footer = $options['footer'] )
			$message .= $footer;
		
		$_options = compact( 'from', 'to', 'cc', 'bcc', 'replyto', 'subject', 'message', 'attachments' );
		
		return $_options;
	}

	/**
	 * 
	 */
	 function email_html( $subject, $fields ){
		$output  = '';
		$output .= '<table cellpadding="0" cellspacing="10" border="0" align="center">';
		$output .= '<tr>';
		$output .= '<td width="600" valign="top" colspan="2">' .sprintf( __( 'Nouvelle demande sur le site %1$s, <a href="%2$s">%2$s<a>'), get_bloginfo('name'), esc_url( get_bloginfo('url') ) ). '</td>';
		$output .= '</tr>';	
		$output .= '<tr>';
		$output .= '<td width="600" valign="top" colspan="2"><h3>'. htmlentities( $subject, ENT_COMPAT, 'UTF-8' ) .'</h3></td>';
		$output .= '</tr>';
			
		foreach( $fields as $field ) :
			if( $field['type'] == 'hidden' ) continue;
			if( $field['type'] == 'file' ) continue;
			if( $field['add-ons']['mailer']['ignore'] ) continue;			
			$output .= '<tr>';
			if( $field['label'] ) :
				$output .= '<td width="200" valign="top">'. htmlentities( stripslashes( $field['label'] ), ENT_COMPAT, 'UTF-8' ) .'</td>';
				$output .= '<td width="400" valign="top">';
			else :
				$output .= '<td colspan="2" width="600" valign="top">';
			endif;		
			if( is_string( $field['value']) ) :
				$output .=  htmlspecialchars_decode( stripslashes( $this->mkcf->fields->translate_value( $field['value'], $field['choices'], $field ) ), ENT_COMPAT );
			elseif( is_array( $field['value'] ) ) :
				$n = 0;
				foreach( $field['value'] as $value ) :				
					if( $n++) $output .= ', ';
					$output .= '<img src="'. $this->tiFy->uri .'/plugins/forms/images/checked.png" align="top" width="16" height="16"/>&nbsp;';		
					$output .= htmlentities( stripslashes( $this->mkcf->fields->translate_value( $value, $field['choices'], $field ) ), ENT_COMPAT, 'UTF-8' );
				endforeach;	
			endif;		
			$output .= '</td>';
			$output .= '</tr>';
		endforeach;
		/*
		$output .= "<tr>";
		$output .= "<td width="600" valign="top" colspan="2">".sprintf( __('Répondre à : <a href="%1$s">%1$s<a>', 'mktzr_forms' ), $mktzr_forms->_submit['request']['email']['value'] )."</td>";
		$output .= "</tr>";*/
		
		$output .= '</table>';
	
		return $output;
	 }

	/**
	 * Traitement des contact
	 */
	function parse_contact( $contact, $fields ){
		$output = array();
		if( is_array( $contact ) ) :
			if( ! isset( $contact['email'] ) ) :
				foreach( $contact as &$c ) :
					if( $email = $this->parse_contact( $c, $fields ) ) :
						$output[] = $email;
					else :
						$output[] = $c;
					endif;
				endforeach;
			else :
				if( isset( $contact['name'] ) ) :
					if( $name = $this->mkcf->functions->translate_field_value( $contact['name'], $fields ) ) :
						$output['name'] = $name;
					else :
						$output['name'] = $contact['name'];
					endif;
				endif;
				if( ( $email = $this->mkcf->functions->translate_field_value( $contact['email'], $fields ) ) && is_email( $email ) ) :
					$output['email'] = $email;
				else :
					$output['email'] = $contact['email'];
				endif;
			endif;
		else :
			if( ( $email = $this->mkcf->functions->translate_field_value( $contact, $fields ) ) && is_email( $email ) ) :
				$output = $email;
			else :
				$output = $contact;
			endif;
		endif;
		
		return $output;
	}
}

/**
 * Message de notification Raw
 * 
 * @todo A FAIRE
 */
function mktzr_forms_email_raw_notification( ){
	global $mktzr_forms;
	
	$output = "";
	$output .= sprintf( __('Vous avez reçu nouvelle demande sur le site %1$s, %2$s'), get_bloginfo('name'), get_bloginfo('url') );
	$output .= "\n\n$subject\n";
	foreach( $mktzr_forms->handle->parsed_request['fields'] as $request ) :
		if( $field['type'] == 'hidden' ) continue;		
		$output .= "\n - ".$request['label']."\t\t\t: ".$request['value']."\n";
	endforeach;	
		
	return $output;	
}