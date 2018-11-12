<?php
class tiFy_Wistify_Update{
	/* = ARGUMENT = */
	public	// Configuration
			$version,
			
			// Référence
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Wistify_Master $master ){
		// Référence
		$this->master = $master;
		
		// Configuration
		$this->version = $this->master->version_installed;
		
		add_action( 'init', array( $this, 'wp_init' ), 99 );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation de Wordpress == **/
	function wp_init(){
		if( version_compare( $this->version, 1507081800, '<' ) )
			$this->update_uids( );	
		if( version_compare( $this->version, 1507152015, '<' ) )
			$this->update_beta_tables( );		
	}
	
	/* = UPDATE VERSION = */
	function update_version( $version = null ){
		if( ! $version )
			$version = $this->master->version;
		update_option( 'wistify_installed_version', $version );
	} 
	
	/* = MISES A JOUR = */	
	/** == Ajout des uids == **/
	function update_uids(){
		global $wpdb;

		require_once( ABSPATH .'wp-admin/install-helper.php' );
		
		maybe_add_column( 
			"$wpdb->wistify_campaign", 
			'campaign_uid', 
			"ALTER TABLE $wpdb->wistify_campaign ADD campaign_uid VARCHAR(32) NULL AFTER campaign_id;" 
		);
		if( $campaign_ids = $wpdb->get_col( "SELECT campaign_id FROM $wpdb->wistify_campaign WHERE 1 AND {$wpdb->wistify_campaign}.campaign_uid IS NULL" ) )
			foreach( $campaign_ids as $campaign_id )
				$wpdb->update( $wpdb->wistify_campaign, array( 'campaign_uid' => tify_generate_token() ), array( 'campaign_id' => $campaign_id ) );
						
		maybe_add_column( 
			"$wpdb->wistify_subscriber", 
			'subscriber_uid', 
			"ALTER TABLE $wpdb->wistify_subscriber ADD subscriber_uid VARCHAR(32) NULL AFTER subscriber_id;" 
		);
		if( $subscriber_ids = $wpdb->get_col( "SELECT subscriber_id FROM $wpdb->wistify_subscriber WHERE 1 AND {$wpdb->wistify_subscriber}.subscriber_uid IS NULL" ) )
			foreach( $subscriber_ids as $subscriber_id )
				$wpdb->update( $wpdb->wistify_subscriber, array( 'subscriber_uid' => tify_generate_token() ), array( 'subscriber_id' => $subscriber_id ) );	
	
		$this->update_version( 1507081800 );
	} 
	
	/** == == **/
	function update_beta_tables(){
		global $wpdb;
		require_once( ABSPATH .'wp-admin/install-helper.php' );
		
		// Table des campagnes
		$wpdb->query( "ALTER TABLE $wpdb->wistify_campaign CHANGE `campaign_id` `campaign_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;" );
		$wpdb->query( "ALTER TABLE $wpdb->wistify_campaign CHANGE `campaign_author` `campaign_author` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';" );
		$wpdb->query( "ALTER TABLE $wpdb->wistify_campaign CHANGE `campaign_date` `campaign_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';" );
		$wpdb->query( "ALTER TABLE $wpdb->wistify_campaign CHANGE `campaign_status` `campaign_status` VARCHAR(25) NOT NULL DEFAULT 'draft';" );
		$wpdb->query( "ALTER TABLE $wpdb->wistify_campaign CHANGE `campaign_step` `campaign_step` INT(2) NOT NULL DEFAULT '0';" );
		maybe_drop_column(
			"$wpdb->wistify_campaign",
			"campaign_send_status",
			"ALTER TABLE $wpdb->wistify_campaign DROP `campaign_send_status`;"
		);
		// Table des listes de diffusion
		$wpdb->query( "ALTER TABLE $wpdb->wistify_list CHANGE `list_id` `list_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;" );
		$wpdb->query( "ALTER TABLE $wpdb->wistify_list CHANGE `list_description` `list_description` LONGTEXT  NULL;" );
		maybe_add_column( 
			"$wpdb->wistify_list",
			"list_menu_order",
			"ALTER TABLE $wpdb->wistify_list ADD `list_menu_order` BIGINT(20) NOT NULL DEFAULT '0' AFTER `list_status`;" 
		);
		maybe_add_column( 
			"$wpdb->wistify_list", 
			"list_public", 
			"ALTER TABLE $wpdb->wistify_list ADD `list_public` TINYINT(1) NOT NULL DEFAULT '1' AFTER `list_menu_order`;"
		);
		// Table des relations listes de diffusion <> abonnés
		maybe_add_column(
			"$wpdb->wistify_list_relationships",
			"rel_subscriber_id",
			"ALTER TABLE $wpdb->wistify_list_relationships CHANGE `subscriber_id` `rel_subscriber_id` BIGINT(20) UNSIGNED NOT NULL" 
		);
		maybe_add_column(
			"$wpdb->wistify_list_relationships",
			"rel_list_id",
			"ALTER TABLE $wpdb->wistify_list_relationships CHANGE `list_id` `rel_list_id` BIGINT(20) UNSIGNED NOT NULL" 
		);
		$wpdb->query( "ALTER TABLE $wpdb->wistify_list_relationships MODIFY COLUMN `rel_list_id` BIGINT(20) UNSIGNED NOT NULL AFTER `rel_subscriber_id`" );
		maybe_add_column(
			"$wpdb->wistify_list_relationships",
			"rel_id",
			"ALTER TABLE $wpdb->wistify_list_relationships DROP PRIMARY KEY, ADD `rel_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`rel_id`);" 
		);
		// Table des abonnés
		$wpdb->query( "ALTER TABLE $wpdb->wistify_subscriber CHANGE `subscriber_id` `subscriber_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;" );
		$wpdb->query( "ALTER TABLE $wpdb->wistify_subscriber CHANGE `subscriber_date` `subscriber_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';" );
		$wpdb->query( "ALTER TABLE $wpdb->wistify_subscriber CHANGE `subscriber_modified` `subscriber_modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';" );
		$wpdb->query( "ALTER TABLE $wpdb->wistify_subscriber CHANGE `subscriber_status` `subscriber_status` VARCHAR(25) NOT NULL DEFAULT 'registred';" );
		
		$this->update_version( 1507152015 );
	}	
}
