<?php
class tify_forms_addon_cookie_transport{
	private $mkcf;
	
	/**
	 * Initialisation
	 */
	function __construct( MKCF $mkcf ){
		// MKCF	
		$this->mkcf 	= $mkcf;
		
		// Définition des options par défaut
		$this->set_default_form_options();
		$this->set_default_field_options();
				
		// Callbacks
		$this->mkcf->callbacks->addons_set( 'field_value', 'cookie_transport', array( $this, 'cb_field_value' ) );
		$this->mkcf->callbacks->addons_set( 'handle_before_redirect', 'cookie_transport', array( $this, 'cb_handle_before_redirect' ) );
	}
	
	/**
	 * Définition des options par défaut pour les formulaire
	 */
	function set_default_form_options(){
		$this->mkcf->addons->set_default_form_options( 'cookie_transport', 
			array(
				'expire' 	=> 30 * DAY_IN_SECONDS,
				'name'		=> 'mktzr_forms_cookie_transport_%s'
			) 
		);
	}
	
	/**
	 * Définition des options par défaut pour les champs de formulaire
	 */
	function set_default_field_options(){
		$this->mkcf->addons->set_default_field_options( 'cookie_transport', 
			array( 
				'ignore' => false 
			) 
		);
	}
	
	/**
	 * CALLBACKS
	 */
	/**
	 * Translation de la valeur du champ si elle existe dans le cookie
	 */
	function cb_field_value( &$field_value, $field ){
		// Bypass
		if( $field['type'] == 'file' )
			return;
		
		$name = sprintf( $this->mkcf->addons->get_form_option( 'name', 'cookie_transport' ), $this->mkcf->forms->get_prefix() .'_'. $this->mkcf->forms->get_ID() );

		if( $datas = $this->cookie_get( $name ) )			
			if(  ! $this->mkcf->addons->get_field_option( 'ignore', 'cookie_transport', $field ) && isset( $datas[ $field['slug'] ] ) )
				$field_value = $datas[ $field['slug'] ];
	}	
	
	/**
	 * Enregistrement du cookie
	 */
	function cb_handle_before_redirect( $parsed_request, $original_request ){
		$datas 		= array();
		$options 	= $this->mkcf->addons->get_options( 'cookie_transport' );
		$expire 	= $this->mkcf->addons->get_form_option( 'expire', 'cookie_transport' );
		$name 		= sprintf( $this->mkcf->addons->get_form_option( 'name', 'cookie_transport' ), $this->mkcf->forms->get_prefix() .'_'. $this->mkcf->forms->get_ID() );
	
		foreach( $parsed_request['values'] as $k => $r )
			if( $parsed_request['fields'][$k]['type'] == 'file' )
				continue;
			elseif( ! $parsed_request['fields'][$k]['add-ons']['cookie_transport']['ignore'] )
				$datas[$k] = $r;

		
		$this->cookie_set( $name, $datas, $expire );
	}
	
	/**
	 * CONTRÔLEURS
	 */
	/**
	 * Création du cookie
	 */
	function cookie_set( $name, $datas, $expire ){
		setcookie( $name, base64_encode( serialize( ' ' ) ), time() - $expire, SITECOOKIEPATH );
		setcookie( $name, base64_encode( serialize( $datas ) ), time() + $expire, SITECOOKIEPATH );
	}
	
	/**
	 * Récupération de la valeur du cookie
	 */
	function cookie_get( $name, $type = 'datas' /* | raw */ ){
		if( ! isset( $_COOKIE[ $name ] ) )
			return;
		switch( $type ) :
			default :
				return unserialize( base64_decode( $_COOKIE[ $name ] ) );
				break;
			case 'raw' :
				return ;
				break;
		endswitch;
	}
}