<?php
class tiFy_Mailer{
	public 
			// Conservation des arguments d'origine
			$args = array(), 	
			// Argument parsés
			$from, $replyto, $cc, $bcc, $to, $subject, $message, $type, $attachments,
			// Entête du mail
			$headers  = array();	
		
	/**
	 * Traitement des arguments
	 */
	function parse_args( $args = array() ){
		$defaults = array(
			// Requis
			'to' 			=> '',
			'subject' 		=> '',
			'message' 		=> '',
			// Optionnels
			'from' 			=> '',
			'replyto' 		=> '',
			'cc' 			=> '',
			'bcc' 			=> '',			
			'type' 			=> 'html',
			'attachments' 	=> array()
		);
		extract( wp_parse_args( $this->args, $defaults ) );
		
		$this->from = $from;
		$this->replyto = $replyto;
		$this->cc = $cc;
		$this->bcc = $bcc;
		$this->to = $to;
		$this->subject = $subject;
		$this->message = $message;
		$this->type = $type;
		$this->attachments = $attachments;
	}	
	
	/**
	 * Préparation de l'email
	 * @param array( from, replyto, cc, bcc, to, subject, message, type )
	 */
	public function prepare( $args = array() ){
		$this->args = $args;
		// Traitement des arguments
		$this->parse_args( $this->args );
		// Expediteur
		$this->from = $this->parse_contact( $this->from );
		// Réponse à
		if( !$this->replyto )
			$this->replyto = $this->from;
		else
			$this->replyto = $this->parse_contact( $this->replyto );
		// Destinataire(s)
		$this->to = $this->parse_contact( $this->to );
		// Destinataire(s) en copie
		$this->cc = $this->parse_contact( $this->cc );
		// Destinataire(s) en copie cachée
		$this->bcc = $this->parse_contact( $this->bcc );
		// Sujet
		$this->subject = stripslashes( $this->subject );
		// Message	
		if( $this->type == 'html' )	
			$this->message = $this->html_message( $this->subject, $this->message );	
		else 
			$message = $this->raw_message( $this->message );	
		// Construction de l'Entête	du mail
		//@TODO  @see http://openclassrooms.com/courses/e-mail-envoyer-un-e-mail-en-php
		$boundary = "-----=". md5(rand());
		//=====Création du header de l'e-mail.
		$this->headers[] = "From: {$this->from}"; // "From: [display_name1] <[email1]>, [display_name2] <[email2]>\r\n";
		$this->headers[] = "Reply-To: {$this->replyto}"; // "Reply-To: [display_name1] <[email1]>, [display_name2] <[email2]>\r\n";
		$this->headers[] = "MIME-Version: 1.0";
		if( $this->type == 'html' )	
			$this->headers[] = "Content-type: text/html; charset=UTF-8";
		else
			$this->headers[] = "Content-Type: text/plain; charset=utf-8";
		//$this->headers[] = "Content-Type: multipart/alternative;";
		//$this->headers[] = " boundary=\"$boundary\"";
	
		if( $this->cc )
			$this->headers[] = "Cc: {$this->cc}"; // "Cc: [display_name1] <[email1]>, [display_name2] <[email2]>\r\n";
		if( $this->bcc )
			$this->headers[] = "Bcc: {$this->bcc}"; // "Bcc: [display_name1] <[email1]>, [display_name2] <[email2]>\r\n";
		//$this->headers[] = "Subject: {$this->subject}";
		$this->headers[] = "Date: ". date('r', current_time('timestamp') );
		$this->headers[] = "X-Mailer: PHP/". phpversion();
	}
	
	/**
	 * 
	 */
	function parse_contact( $contact = null ){
		// Bypass
		if( ! $contact )
			return;
		$output = "";
		if( is_array( $contact ) ) :
			if( ! isset( $contact['email'] ) ) :
				$contacts = array();
				foreach( $contact as &$c )
					$contacts[] = $this->parse_contact( $c );
				if( $contacts )
					return implode( ',', $contacts );
				else				
					wp_die( __( 'Format de contact invalide', 'tify' ) );
			elseif( isset( $contact['name'] ) && is_email( $contact['email'] ) ) :
				$output .= $contact['name'];
				$output .= " <".$contact['email'].">";
			elseif( is_email( $contact['email'] ) ):
				$output .= $contact['email'];
			endif;
		elseif( is_email( $contact ) ) :
			$output = $contact;
		endif;
		
		return $output;			
	}
	
	/**
	 * @todo
	 */
	function raw_message( $subject, $message ){
		$output  = "";
		$output .= sprintf( __( 'Vous avez reçu nouvelle demande sur le site %1$s, %2$s', 'tify' ), get_bloginfo('name'), get_bloginfo('url') )."\n";
		$output .= "\n\n$subject\n";
		$output .= $message;
			
		return $output;	
	}
	
	/**
	 * Formatage du message en HTML
	 */
	static function html_message( $subject, $message ){
		$output  = "";
		$output .= "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>\n";
		$output .= "<html xmlns='http://www.w3.org/1999/xhtml'>\n";
		$output .= 	"<head>\n";
    	$output .= 		"<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
    	$output .= 		"<title>{$subject}</title>\n";     
		$output .= 		"<style type='text/css'>\n";
        $output .= 			".ExternalClass {width:100%;}\n";
        $output .= 			".ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {\n";
        $output .= 				"line-height: 100%;\n";
        $output .= 			"}\n"; 
       	$output .= 			"body {-webkit-text-size-adjust:none; -ms-text-size-adjust:none;}\n";
       	$output .= 			"body {margin:0; padding:0;}\n";
        $output .= 			"table td {border-collapse:collapse;}\n";   
        $output .= 			"p {margin:0; padding:0; margin-bottom:0;}\n";
        $output .= 			"h1, h2, h3, h4, h5, h6 {\n";
        $output .= 			"color: black;\n";
        $output .= 				"line-height: 100%;\n";
        $output .= 			"}\n"; 
        $output .= 			"a, a:link {\n";
        $output .= 				"color:#2A5DB0;\n";
        $output .= 				"text-decoration: underline;\n";
        $output .= 			"}\n";
		$output .= 			"body, #body_style {\n";
		$output .= 				"background:#FFF;\n";
       // $output .= 				"min-height:1000px;\n";	
        $output .= 				"color:#000;\n";
        $output .= 				"font-family:Arial, Helvetica, sans-serif;\n";
        $output .= 				"font-size:12px;\n";
        $output .= 			"}\n";
        $output .= 			"span.yshortcuts { color:#000; background-color:none; border:none;}\n";
        $output .= 			"span.yshortcuts:hover,\n";
        $output .= 			"span.yshortcuts:active,\n";
        $output .= 			"span.yshortcuts:focus {color:#000; background-color:none; border:none;}\n";                 
        $output .= 			"a:visited { color: #3c96e2; text-decoration: none}\n";
        $output .= 			"a:focus   { color: #3c96e2; text-decoration: underline}\n"; 
        $output .= 			"a:hover   { color: #3c96e2; text-decoration: underline}\n"; 
        $output .= 			"@media only screen and (max-device-width: 480px) {\n";                    
		$output .= 				"body[yahoo] #container1 {display:block !important}\n"; 
        $output .= 				"body[yahoo] p {font-size: 10px}\n";
        $output .= 			"}\n";      
        $output .= 			"@media only screen and (min-device-width:768px) and (max-device-width:1024px){\n";
        $output .= 				"body[yahoo] #container1 {display:block !important}\n";
        $output .= 				"body[yahoo] p {font-size: 12px}\n";                   
        $output .= 			"}\n";            
		$output .= 		"</style>\n";
		$output .= 	"</head>\n";
		$output .= 	"<body style='background:#FFF; color:#000;font-family:Arial, Helvetica, sans-serif; font-size:12px' alink='#FF0000' link='#FF0000' bgcolor='#FFFFFF' text='#000000' yahoo='fix'>\n";
		$output .= 		"<div id='body_style' style='padding:15px'>\n";
		$output .= 			"<table cellpadding='0' cellspacing='0' border='0' bgcolor='#FFFFFF' width='600' align='center'>\n";
        $output .= 				"<tr>\n";
        $output .= 					"<td width='600'>{$message}</td>\n";
        $output .= 				"</tr>\n";
        $output .= 			"</table>\n";
		$output .= 		"</div>\n"; 
		$output .= 	"</body>\n";
		$output .= "</html>";
		
		return $output;
	}
	
	/**
	 * Affichage du mail en mode debug
	 */
	public function debug_email(){
		$output  = "";
		$output .= "<div style=\"background-color:#EEEEEE; margin:-1em -2em 2em; padding:1em 2em\">";
		$output .= "\n\t<table cellpadding=\"0\" cellspacing=\"0\">";		
		$output .= "\n\t\t<tbody>";

		foreach( $this->headers as $header ) :
			$output .= "\n\t\t\t<tr>";
			$output .= "\n\t\t\t\t<td>";
			$output .= htmlspecialchars( $header );
			$output .= "\n\t\t\t\t</td>";
			$output .= "\n\t\t\t</tr>";			
		endforeach;
		
		$output .= "\n\t\t</tbody>";
		$output .= "\n\t</table>";
		$output .= "\n</div>";
		
		echo $this->html_message( $this->subject, $output . $this->args['message'] );
		exit;
	}
	
	/**
	 * Envoi du mail
	 */
	public function send(){
		wp_mail( $this->to, $this->subject, $this->message, $this->headers, $this->attachments );
	}	
}