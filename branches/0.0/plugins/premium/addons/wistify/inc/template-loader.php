<?php
class tiFy_Wistify_TemplateLoader{
	/* = ARGUMENTS = */
	public	// Référence
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Wistify_Master $master ){
		// Référence
		$this->master = $master;
		
		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'template_redirect', array( $this, 'wp_template_redirect' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation global == **/
	function wp_init(){
		// Déclaration de la variable de requête 
		add_rewrite_tag( '%wistify%', '([^&]+)' );
		// Déclaration de la régle de réécriture
		$rewrite_rules = get_option( 'rewrite_rules' );
		if( ! in_array( '^wistify/?', array_keys( $rewrite_rules ) ) ) :
			add_rewrite_rule( '^wistify/?', 'index.php?wistify=true', 'top' );
			flush_rewrite_rules( );
			wp_redirect( ( stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		endif;
	}
	
	/** == Affichage en ligne d'une campagne == **/ 
	function wp_template_redirect(){
		// Bypass
		if( ! get_query_var('wistify') )
			return;		
		if( ! preg_match( '/\/wistify\/(.*)\//', $_SERVER['REQUEST_URI'], $action ) )
			return;
		
		switch( $action[1] ) :
			case 'archive' :
				$this->tpl_archive();
				break;
			case 'unsubscribe' :
				$this->tpl_unsubscribe();
				break;
			default :
				$this->tpl_404();
				break;
		endswitch;
	
		exit;
	}
	
	/* = TEMPLATE = */
	/** == 404 == **/
	function tpl_404(){
		echo 'Wistify 404';	
	}
	
	/** == Affichage de la campagne en ligne == **/
	function tpl_archive(){
		if( empty( $_REQUEST['c'] ) )
			return $this->tpl_404();
		if( empty( $_REQUEST['u'] ) )
			return $this->tpl_404();
		
		// Récupération de la campagne
		$campaign_query	= new tiFy_Wistify_Campaigns_Db;	
		if( ! $c = $campaign_query->get_item_by( 'uid', $_REQUEST['c'] ) )
			return $this->tpl_404();
		
		// Récupération de l'abonné
		$subscriber_query	= new tiFy_Wistify_Subscribers_Db;
		if( ! $u = $subscriber_query->get_item_by( 'uid', $_REQUEST['u'] ) )
			$u = get_transient( 'wty_account_'. $_REQUEST['u'] );

		if( ! $u ) return $this->tpl_404();
		
		// Affichage de la campagne
		echo $this->master->queue->html_message( $c->campaign_title, $c->campaign_content_html );		
	}
	
	/** == Affichage du formulaire de désinscription == **/
	function tpl_unsubscribe(){
		if( empty( $_REQUEST['c'] ) )
			return $this->tpl_404();
		if( empty( $_REQUEST['u'] ) )
			return $this->tpl_404();
		
		// Récupération de la campagne
		$campaign_query	= new tiFy_Wistify_Campaigns_Db;	
		if( ! $c = $campaign_query->get_item_by( 'uid', $_REQUEST['c'] ) )
			return $this->tpl_404();
		
		// Récupération de l'abonné
		$subscriber_query	= new tiFy_Wistify_Subscribers_Db;
		if( $u = $subscriber_query->get_item_by( 'uid', $_REQUEST['u'] ) )
			$subscriber_query->update_item( $u->subscriber_id, array( 'subscriber_status' => 'unsubscribed' ) );
		else
			$u = get_transient( 'wty_account_'. $_REQUEST['u'] );
		
		if( ! $u ) return $this->tpl_404();
		
		// Affichage de la confirmation de désinscription
		_e( 'Vous désormais désinscrit de la newsletter', 'tify' );		
	}
}