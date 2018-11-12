<?php
/**
 * Usage :
 * 
 	add_filter( 'tify_mandrill_api_key', '{function_hook_name}' );
	function function_hook_name( $key ){
		return ''; // Clé D'API Mandrill (requis)
	}
 */
/**
 * @see https://github.com/darrenscerri/Mindrill
 * @see https://github.com/kai5263499/mandrill-php
 */ 

require_once dirname( __FILE__ ) .'/mandrill-api-php/Mandrill.php';	

class tiFy_Mandrill extends Mandrill{	
	/* = CONSTRUCTEUR = */
	function __construct( $key = null ){
		// Instanciation de l'API Mandrill
		parent::__construct( $key );

		/// Forcer l'IPV4
		curl_setopt( $this->ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
			
		// Actions et Filtres Wordpress
		add_action( 'wp_ajax_tify_mandrill', array( $this, 'wp_ajax_action' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Lancement de methode de l'API via Ajax == **/
	function wp_ajax_action(){
		$ajax_response = call_user_func( array( $this, $_REQUEST['api'] .'_'. $_REQUEST['method'] ), $_REQUEST['args'] );
		
		echo json_encode( $ajax_response );
		exit;
	}
	
	/* = API MESSAGES = */
	/** == Envoi de message == 
	 * @see https://mandrillapp.com/api/docs/messages.php.html#method=send
	 **/
	function messages_send( $args = array() ){
		$defaults = array(
			'message'	=> array(
				'to' => array(
					array(
						'email'	=> ''	// Requis
					)
				)
			),
			'async'		=> false,
			'ip_pool'	=> null,
			'send_at'	=> null
		);
		$args = wp_parse_args( $args, $defaults );
		$args['message']['html'] =  stripslashes_deep( $args['message']['html'] );

		$result = '';
		try {
			$result = $this->messages->send( $args['message'], $args['async'], $args['ip_pool'], $args['send_at'] );		
		} catch( Mandrill_Error $e ) {
			$result = new WP_Error( get_class($e), $e->getMessage() );
		}
		return $result;		
	}
	/** == Envoi de message utilisant un gabarit == 
	 * @see https://mandrillapp.com/api/docs/messages.php.html#method=send-template
	 **/
	function messages_send_template( $args = array() ){
		$defaults = array(
			'template_name' 	=> '',	// Requis
			'template_content'	=> array(
				array(
					'name'		=> '',
					'content'	=> ''
				)
			),
			'message'	=> array(
				'to' => array(
					array(
						'email'	=> ''	// Requis
					)
				)
			),
			'async'		=> false,
			'ip_pool'	=> null,
			'send_at'	=> null
		);
		$args = wp_parse_args( $args, $defaults );
		
		$result = '';
		try {
			$result = $this->messages->sendTemplate( $args['template_name'], $args['template_content'], $args['message'], $args['async'], $args['ip_pool'], $args['send_at'] );		
		} catch( Mandrill_Error $e ) {
			$result = new WP_Error( get_class($e), $e->getMessage() );
		}
		return $result;
	}
	/** == Envoi de message utilisant un gabarit == 
	 * @see https://mandrillapp.com/api/docs/messages.php.html#method=info
	 **/
	function messages_info( $args = array() ){
		$defaults = array(
			'id' => 0	
		);
		$args = wp_parse_args( $args, $defaults );
		
		$result = '';
		try {
			$result = $this->messages->info( $args['id'] );		
		} catch( Mandrill_Error $e ) {
			$result = new WP_Error( get_class($e), $e->getMessage() );
		}
		return $result;
	}
	
	/* = API TEMPLATES = */
	/** == Récupération des données d'un template == 
	 * @see https://mandrillapp.com/api/docs/templates.php.html#method=info
	 **/ 
	function templates_get_info( $args = array() ){
		$defaults = array(
			'name' => ''	// Requis
		);
		$args = wp_parse_args( $args, $defaults );
		// Bypass
		if( empty( $args['name'] ) ) return;
		
		$result = '';
		try {
			$result = $this->templates->info( $args['name'] );		
		} catch( Mandrill_Error $e ) {
			$result = new WP_Error( get_class($e), $e->getMessage() );
		}
		return $result;
	}
	/** == Récupération de la liste des templates == 
	 * @see https://mandrillapp.com/api/docs/templates.php.html#method=list
	 */
	function templates_get_list( $args = array() ){
		$defaults = array(
			'label' => ''
		);
		$args = wp_parse_args( $args, $defaults );
		
		$results = array();
		try {
			$results = $this->templates->getList( $args['label'] );		
		} catch( Mandrill_Error $e ) {
			$result = new WP_Error( get_class($e), $e->getMessage() );
		}
		return $results;
	}
}