<?php
/**
 * Acheminement des messages
 */
class tiFy_Wistify_Queue_Main{
	/* = ARGUMENTS = */
	public	// Contrôleur
			$master,
			$campaigns_query,	
			$subscribers_query,
			$mailing_lists_query,
			$queue_query,
			$reports_query,
			// Configuration
			$token_prefix;
			
			
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Wistify_Master $master ){
		// Configuration
		$this->master 		= $master; // Controleur principal
		$this->db			= new tiFy_Wistify_Queue_Db;				
		
		// Configuration
		$this->token_prefix = 'wistify_send_campaign_';
		
		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'wp_ajax_wistify_campaign_prepare', array( $this, 'wp_ajax_prepare' ) );
		add_action( 'wp_ajax_wistify_campaign_prepare_recipients_subscriber', array( $this, 'wp_ajax_prepare_recipients_subscriber' ) );
		add_action( 'wp_ajax_wistify_campaign_prepare_recipients_mailing_list', array( $this, 'wp_ajax_prepare_recipients_mailing_list' ) );
		add_action( 'wp_ajax_wistify_campaign_send_emails', array( $this, 'wp_ajax_send_emails' ) );		
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation globale == **/
	function wp_init(){
		// Contrôleur de base de données
		$this->campaigns_query 		= new tiFy_Wistify_Campaigns_Db;
		$this->subscribers_query 	= new tiFy_Wistify_Subscribers_Db;
		$this->mailing_lists_query 	= new tiFy_Wistify_MailingLists_Db;
		$this->reports_query 		= new tiFy_Wistify_Reports_Db;
		
		//var_dump( $this->subscribers_query->get_items_col( 'email', array( 'list_id' => 98, 'status'=> 'registred', 'per_page' => 1, 'paged' => 250 ) ) );exit;
	}
	
	/** == Lancement de la préparation de la campagne == **/
	function wp_ajax_prepare(){
		$recipients = false;
		
		$types = array();
		$count = array(); 
		$total = 0;
		
		// Récupération des variables
		$campaign_id = (int) $_REQUEST['campaign_id'];
						
		// Suppression du cache
		delete_transient( $this->token_prefix . $campaign_id  );
		// Nettoyage des messages à envoyer
		$this->sanitize_sends( $campaign_id );		
				
		// Compte le nombre d'emails à envoyer
		if( $recipients = $this->campaigns_query->get_item_var( $campaign_id, 'recipients' ) ) :
						
			/// Abonnés Wistify
			if( isset( $recipients['wystify_subscriber'] ) ) :
				$count['wystify_subscriber'] = 0;
				foreach( $recipients['wystify_subscriber'] as $subscriber_id ) :
					if( $this->subscribers_query->get_item_var( $subscriber_id, 'status' ) === 'registred' ) :
						$count['wystify_subscriber']++; 
						$total++;
					endif;
				endforeach;		
				if( $count['wystify_subscriber'] )
					array_push( $types, 'wystify_subscriber' );	
			endif;

			/// Listes de diffusion Wistify	
			if( isset( $recipients['wystify_mailing_list'] ) ) :
				$count['wystify_mailing_list'] = 0;
				
				foreach( $recipients['wystify_mailing_list'] as $wml ) :					
					if( ! $c = $this->subscribers_query->count_items( array( 'list_id' => $wml, 'status' => 'registred' ) ) )
						continue;
					if( ! isset( $count['wystify_mailing_list'] ) )
						$count['wystify_mailing_list'] = 0;										 
					$count['wystify_mailing_list'] += $c; 
					$total += $c;
				endforeach;
				if( $count['wystify_mailing_list'] )
					array_push( $types, 'wystify_mailing_list' );				
			endif;				
		endif;
				
		echo json_encode( array( 'recipients' => $recipients, 'types' => $types, 'count' => $count, 'total' => $total  ) );
		exit;
	}

	/** == Préparation des abonnés == **/
	function wp_ajax_prepare_recipients_subscriber(){
		foreach( $_REQUEST['subscriber_ids'] as $subscriber_id )		
			$emails[] = $this->subscribers_query->get_item_var( $subscriber_id, 'email' );		
		$total = count( $emails );
		
		$duplicates = $this->prepare_sends( $emails, $_REQUEST['campaign_id'] );
					
		echo json_encode( array( 'total' => $total, 'emails' => $emails, 'duplicates' => $duplicates ) );
		exit;
	}
		
	/** == Préparation des abonnés d'une liste de diffusion == **/
	function wp_ajax_prepare_recipients_mailing_list(){
		$emails = $this->subscribers_query->get_items_col( 'email', array( 'list_id' => $_REQUEST['list_id'], 'status'=> 'registred', 'per_page' => $_REQUEST['per_page'], 'paged' => $_REQUEST['paged'] ) );
		$total = count( $emails );	
		$duplicates = $this->prepare_sends( $emails, $_REQUEST['campaign_id'] );		
		
		echo json_encode( array( 'total' => $total, 'emails' => $emails, 'duplicates' => $duplicates ) );
		exit;
	}
		
	/** == Envoi des emails de la campagne == **/
	function wp_ajax_send_emails(){
		global $wpdb;
				
		// Récupération des variables
		$campaign_id = (int) $_REQUEST['campaign_id'];
		$token_key = $this->token_prefix . $campaign_id;	
		
		$token_args = array(
			'total' 		=> 0,
			'processed'		=> 0,
			'last_info_id'	=> 0,
			'last_datetime'	=> '0000-00-00 00:00:00'			
		);
		
		// Vérification du jeton d'envoi
		if( $token = get_transient( $token_key ) ) :	
			$token_args = wp_parse_args( $token, $token_args );
		else :	
		// Création du jeton d'envoi
			$token_args['campaign_id'] = $campaign_id; 
			if( ! $total = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(queue_email) FROM {$this->db->wpdb_table} WHERE queue_campaign_id = %d", $campaign_id ) ) )
				wp_die(0);
			$token_args['total'] = (int) $total;
			/// Définition du statut d'envoi de la campagne 
			$this->campaigns_query->update_item( $campaign_id, array( 'campaign_status' => 'in-progress' ) );
			
			$token = set_transient( $token_key, $token_args );			
		endif;
				
		// Récupération du prochain email à traiter		
		if( ! $s = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->db->wpdb_table} WHERE queue_campaign_id = %d LIMIT 1", $campaign_id ) ) ) :
			/// Définition du statut d'envoi de la campagne
			if(  $token['total'] === $token['processed'] )
				$this->campaigns_query->update_item( $campaign_id, array( 'campaign_status' => 'distributed' ) );
			
			delete_transient( $token_key );
			wp_die(0);
		endif;	
		
		// Définition des arguments d'envoi 
		$message_args = $this->prepare_message_args( $campaign_id, $s->queue_email );
		
		// Envoi du message
		$ms = $this->master->Mandrill->messages->send( $message_args );
		$send_result = array_shift( $ms );
		$insert_id = $this->reports_query->insert_item( 
			array(
				'report_campaign_id'		=> $campaign_id,
				'report_md_ts'				=> current_time( 'timestamp', false ),
				'report_md__id'				=> $send_result['_id'],
				'report_md_sender'			=> $message_args['from_email'],
				'report_md_subject'			=> $message_args['subject'],
				'report_md_email'			=> $send_result['email'],
				'report_md_state'			=> 'posted',
				'report_md_reject_reason'	=> $send_result['reject_reason'],
			)
		);
		$wpdb->delete( $this->db->wpdb_table, array( 'queue_email' => $s->queue_email, 'queue_campaign_id' => $s->queue_campaign_id ) );
		
		// Mise à jour du jeton d'envoi
		++$token_args['processed'];
		$token_args['last_info_id'] = $send_result['_id'];
		$token_args['last_datetime'] = current_time( 'mysql' );
		
		set_transient( $token_key, $token_args );
		
		echo json_encode( get_transient( $token_key ) );
		exit;
	}

	/* = CONTRÔLEURS = */
	/** == Préparation des arguments de message == **/
	function prepare_message_args( $campaign_id, $recipient_email = null, $args = array() ){
		$campaign_db = new tiFy_Wistify_Campaigns_Db;
		$campaign = $campaign_db->get_item_by_id( $campaign_id );
		
		$campaign->campaign_message_options['html'] = $campaign->campaign_content_html;  
		$message = wp_parse_args( $args, $campaign->campaign_message_options );
		
		// Traitement du sujet de message
		$message['subject'] = wp_unslash( $message['subject'] );
				
		// Traitement du contenu du message
		/// Lien vers la prévisualisation en ligne
		if( ! preg_match( '/\*\|ARCHIVE\|\*/', $message['html'], $matches ) )		
			$message['html'] = 	"<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"0\" align=\"center\" style=\"border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;\">".
									"<tbody>".
										"<tr>".
											"<td style=\"padding-top: 9px;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #606060;font-family: Helvetica;font-size: 11px;line-height: 125%;text-align: left;\">".
												"<div style=\"text-align: center;\">".
													"<a href=\"*|ARCHIVE|*\" style=\"font-size:11px;word-wrap:break-word;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;color:#606060;font-weight:normal;text-decoration:underline;\">".
									 					__( 'Visualiser ce mail dans votre navigateur internet', 'tify' ).
									 				"</a>".
									 			"</div>".
								 			"</td>".
								 		"</tr>".
								 	"</tbody>".
								 "</table>".
								 $message['html'];
		
		/// Lien de désinscription
		if( ! preg_match( '/\*\|UNSUB\|\*/', $message['html'], $matches ) )		
			$message['html'] = 	$message['html'].
								"<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"0\" align=\"center\" style=\"border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;\">".
									"<tbody>".
										"<tr>".
											"<td style=\"padding-top: 9px;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #606060;font-family: Helvetica;font-size: 11px;line-height: 125%;text-align: left;\">".
												"<div style=\"text-align: center;\">".
													"<a href=\"*|UNSUB|*\" style=\"font-size:11px;word-wrap:break-word;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;color:#606060;font-weight:normal;text-decoration:underline;\">".
									 					__( 'Désinscription', 'tify' ).
									 				"</a>".
									 			"</div>".
								 			"</td>".
								 		"</tr>".
								 	"</tbody>".
								 "</table>";
								
		$message['html'] = $this->html_message( $message['subject'], $message['html'] );
		
		// Traitement du destinataire
		$message['to'][0]['email'] = $recipient_email;
						
		// Traitement des variables
		$vars = array();
		$vars['c'] = $campaign->campaign_uid;
		$subscriber_query = new tiFy_Wistify_Subscribers_Db;
		
		if( ! empty( $_POST['service_account'] ) ) :
			$vars['u'] = $_POST['service_account'];
			set_transient( 'wty_account_'. $vars['u'], $recipient_email, HOUR_IN_SECONDS );
		elseif( $u = $subscriber_query->get_item_by( 'email', $recipient_email ) ) :
			$vars['u'] = $u->subscriber_uid;
		endif;
		
		//// Récupération de l'utilisateur		
		$message['global_merge_vars'] = array(
			array(
				'name' 		=> 'ARCHIVE',
				'content'	=> add_query_arg( $vars, home_url( '/wistify/archive' ) )
			),
			array(
				'name' 		=> 'UNSUB',
				'content'	=> add_query_arg( $vars, home_url( '/wistify/unsubscribe' ) )
			)
		);		
		$message['tags'] = array( 'wty'. $campaign->campaign_uid, substr( $message['subject'], 0, 50 ) );
			
		/// Convertion des valeurs boléennes
		foreach( $message as $k => &$v )
			if( in_array( $k, array( 'important', 'track_opens', 'track_clicks', 'auto_text', 'auto_html', 'inline_css', 'url_strip_qs', 'preserve_recipients', 'view_content_link', 'merge' ) ) )
				$v = filter_var($v, FILTER_VALIDATE_BOOLEAN );
			
		return $message;
	}
		
	function html_message( $subject, $html_template ){
		$output  = "";		
		$output .= "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>\n";
		$output .= "<html xmlns='http://www.w3.org/1999/xhtml'>\n";
		$output .= "<meta content='text/html; charset=UTF-8' http-equiv='Content-Type'>\n";
		$output .= "<meta content='width=device-width, initial-scale=1.0' name='viewport'>\n";
		$output .= "<title>{$subject}</title>\n";
		$output .= "<style type='text/css'>". file_get_contents( $this->master->dir . "/css/html_message.css" ) ."</style>";
		$output .= "<body marginwidth='0' marginheight='0' style='margin: 0;padding: 0;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #F2F2F2;height: 100% !important;width: 100% !important;' offset='0' topmargin='0' leftmargin='0'>\n";
		$output .= "<center>\n";
        $output .= "<table id='bodyTable' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;margin: 0;padding: 0;background-color: #F2F2F2;height: 100% !important;width: 100% !important;' border='0' cellpadding='0' cellspacing='0' width='100%' height='100%' align='center'>\n";
		$output .= "<tbody>\n";
		$output .= "<tr>\n";
        $output .= "<td id='bodyCell' style='mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;margin: 0;padding: 20px;border-top: 0;height: 100% !important;width: 100% !important;' valign='top' align='center'>\n";
        $output .= "<!-- BEGIN TEMPLATE // -->\n";
        $output .= $html_template;
		$output .= "<!-- // END TEMPLATE -->\n";
        $output .= "</td>\n";
        $output .= "</tr>\n";
        $output .= "</tbody>\n";
        $output .= "</table>\n";
        $output .= "</center>\n";
        $output .= "</body>\n";
		
		return $output;
	}
	
	/** == Préparation des envois == **/
	function prepare_sends( $emails, $campaign_id ){
		$duplicates = array();		
		
		foreach( (array) $emails as $email ) :
			if( ! $email ) continue;
			if( $s = $this->db->get_item( array( 'email' => esc_attr( $email ), 'campaign_id' => $campaign_id ) ) ) :
				$duplicates[] = $this->db->update_item( $s->queue_id, array( 'queue_email' => esc_attr( $email ), 'queue_campaign_id' => $campaign_id ) );
			else :
				$duplicates[] = $this->db->insert_item( array( 'queue_email' => esc_attr( $email ), 'queue_campaign_id' => $campaign_id ) );			
			endif;
		endforeach;
		
		return $duplicates;
	}
	
	/** == Nettoyage des envois == **/
	function sanitize_sends( $campaign_id ){
		return $this->db->sanitize_campaign_sends( $campaign_id );
	}
}

/* == GESTION DES DONNEES EN BASE = */
class tiFy_Wistify_Queue_Db extends tiFy_db{
	/* = ARGUMENTS = */
	public	$install = true;
	
	/* = CONSTRUCTEUR = */	
	function __construct( ){		
		// Définition des arguments
		$this->table 		= 'wistify_queue';
		$this->col_prefix	= 'queue_';
		$this->primary_col = 'id';
		$this->has_meta		= false;
		$this->cols			= array(
			'id' 			=> array(
				'type'			=> 'BIGINT',
				'size'			=> 20
			),
			'email' 		=> array(
				'type'			=> 'VARCHAR',
				'size'			=> 255,
				
				'search'		=> true
			),
			'campaign_id'	=> array(				
				'type'			=> 'BIGINT',
				'size'			=> 20,
				'unsigned'		=> true
			)
		);
		
		parent::__construct();				
	}
	
	/* = REQUETES PERSONNALISÉES = */
	function sanitize_campaign_sends( $campaign_id ){
		global $wpdb;
		
		return $wpdb->delete( $this->wpdb_table, array( 'queue_campaign_id' => $campaign_id ), '%d' );		
	}	
}