<?php
class tiFy_Wistify_AjaxActions{
	/* = ARGUMENTS = */
	public	// Référence
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Wistify_Master $master ){
		// Référence
		$this->master = $master;
		
		// ACTIONS ET FILTRES WORDPRESS
		/// Messages
		add_action( 'wp_ajax_wistify_messages_send', array( $this, 'messages_send' ) );
		/// Abonnés
		add_action( 'wp_ajax_wistify_search_autocomplete_recipients', array( $this, 'search_autocomplete_recipients' ) );
	}
	
	/* = ACTIONS = */
	/** == MESSAGES == **/
	/*** === Envoi des messages == **/
	function messages_send(){
		check_ajax_referer( 'wistify_messages_send', '_wty_ajax_nonce' );
				
		$campaign_id = $_POST['campaign_id'];
		$recipient_email = $_POST['recipient_email'];
		$args = $_POST['message'];
		
		$message_args = $this->master->queue->prepare_message_args( $campaign_id, $recipient_email, $args );
				
		$resp = $this->master->Mandrill->messages->send( $message_args );
		wp_die( json_encode( $resp ) );
	}
	
	/** == ABONNES == **/
	/*** === Récupération de déstinataires par autocomplétion === ***/
	function search_autocomplete_recipients(){
		$return = array();
		
		if ( ! empty( $_REQUEST['type'] ) )
			$types = array_map( 'trim', explode( ',', $_REQUEST['type'] ) );
		else
			$types = array( 'subscriber', 'mailing-list' );	
		
		$subscribers_query = new tiFy_Wistify_Subscribers_Db;
		$mailing_lists_query = new tiFy_Wistify_MailingLists_Db;
				
		// Recherche parmi les abonnés Wistify
		if( in_array( 'subscriber', $types ) ) :					
			if( $results = $subscribers_query->get_items( array( 'status' => 'registred', 'search' => $_REQUEST['term'] ) ) ) :
				foreach ( (array) $results as $result ){
					$label				= $result->subscriber_email;
					$type 				= 'wystify_subscriber';
					$type_label			= __( 'Abonné', 'tify' ); 
					$value 				= $result->subscriber_id;
					$ico 				= '<i class="fa fa-user"></i><i class="badge wisti-logo"></i>';
					$_render			= 	"<span class=\"ico\">{$ico}</span>\n". 
											"<span class=\"label\">{$label}</span>\n".
											"<span class=\"type\">{$type_label}</span>\n";
					$render['label'] 	= 	"<a href=\"\">". $_render ."</a>";	
					$render['value'] 	= 	"<li data-numbers=\"1\">\n". 
											"\t". $_render ."\n".
											"\t<a href=\"\" class=\"tify_button_remove remove\"></a>\n".
											"\t<input type=\"hidden\" name=\"{$_REQUEST['name']}[{$type}][]\" value=\"{$value}\">\n".										
											"</li>\n";	
					$return[] = $render;
				}
			endif;
		endif;	
		// Recherche parmi les listes de diffusion Wistify
		if( in_array( 'mailing-list', $types ) ) :		
			if( $results = $mailing_lists_query->get_items( array( 'search' => $_REQUEST['term'] ) ) ) :
				foreach ( (array) $results as $result ){
					$label				= $result->list_title;
					$type 				= 'wystify_mailing_list';
					$type_label			= __( 'Liste de diffusion', 'tify' ); 
					$value 				= $result->list_id;
					$numbers 			= $subscribers_query->count_items( array( 'list_id' => $result->list_id, 'status' => 'registred' ) );
					$ico 				= '<i class="fa fa-group"></i><i class="badge wisti-logo"></i>';
					$_render			= 	"<span class=\"ico\">{$ico}</span>\n". 
											"<span class=\"label\">{$label}</span>\n".
											"<span class=\"type\">{$type_label}</span>\n".
											"<span class=\"numbers\">{$numbers}</span>\n";
					$render['label'] 	= 	"<a href=\"\">". $_render ."</a>";	
					$render['value'] 	= 	"<li data-numbers=\"$numbers\">\n". 
											"\t". $_render ."\n".
											"\t<a href=\"\" class=\"tify_button_remove remove\"></a>\n".
											"\t<input type=\"hidden\" name=\"{$_REQUEST['name']}[{$type}][]\" value=\"{$value}\">\n".
											"\t</li>\n";	
					$return[] = $render;
				}
			endif;
		endif;
		// Recherche parmi les utilisateurs Wordpress
		/*if( in_array( 'wordpress-user', $types ) ) :
			$user_query_args = array(
				'search'         => '*'. $_REQUEST['term'] .'*',
				'search_columns' => array( 'user_login', 'user_email', 'user_nicename' )
			);	
			$user_query = new WP_User_Query( $user_query_args ); 	
			if( $results = $user_query->get_results() ) :
				foreach ( (array) $results as $result ){
					$label				= $result->user_email;
					$type 				= 'wordpress_user';
					$type_label			= __( 'Utilisateur Wordpress', 'tify' ); 
					$value 				= $result->ID;
					$ico 				= '<i class="fa fa-user"></i><i class="badge dashicons dashicons-wordpress"></i>';
					$_render			= 	"<span class=\"ico\">{$ico}</span>\n". 
											"<span class=\"label\">{$label}</span>\n".
											"<span class=\"type\">{$type_label}</span>\n";
					$render['label'] 	= 	"<a href=\"\">". $_render ."</a>";	
					$render['value'] 	= 	"<li data-numbers=\"1\">\n". 
											"\t". $_render ."\n".
											"\t<a href=\"\" class=\"tify_button_remove remove\"></a>\n".
											"\t<input type=\"hidden\" name=\"{$_REQUEST['name']}[{$type}][]\" value=\"{$value}\">\n".
											"\t</li>\n";	
					$return[] = $render;
				}
			endif;
		endif;
		// Recherche parmi les roles Wordpress
		if( in_array( 'wordpress-role', $types ) ) :
			$results = array();
			foreach( get_editable_roles() as $role => $value ) :
				if( preg_match( '/'. preg_quote( $_REQUEST['term'] ) .'/i', translate_user_role( $value['name'] ) ) ) :
				 	$results[$role] = translate_user_role( $value['name'] );	
				endif;
			endforeach;
					
			if( $results ) :
				foreach ( (array) $results as $role_id => $result ){
					
					$label				= $result;
					$type 				= 'wordpress_role';
					$type_label			= __( 'Groupe d\'utilisateurs Wordpress', 'tify' ); 
					$value 				= $role_id;
					$user_query 		= new WP_User_Query( array( 'role' => $role_id ) );
					$numbers			= $user_query->get_total();
					$ico 				= '<i class="fa fa-group"></i><i class="badge dashicons dashicons-wordpress"></i>';
					$_render			= 	"<span class=\"ico\">{$ico}</span>\n". 
											"<span class=\"label\">{$label}</span>\n".
											"<span class=\"type\">{$type_label}</span>\n".
											"<span class=\"numbers\">{$numbers}</span>\n";
					$render['label'] 	= 	"<a href=\"\">". $_render ."</a>";	
					$render['value'] 	= 	"<li data-numbers=\"{$numbers}\">\n". 
											"\t". $_render ."\n".
											"\t<a href=\"\" class=\"tify_button_remove remove\"></a>\n".
											"\t<input type=\"hidden\" name=\"{$_REQUEST['name']}[{$type}][]\" value=\"{$value}\">\n".
											"\t</li>\n";	
					$return[] = $render;
				}
			endif;
		endif;*/	
				
		wp_die( json_encode( $return ) );
	}		
}