<?php
class tiFy_Wistify_Reports_Main{
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Wistify_Master $master ){
		// Configuration
		$this->master 		= $master; // Controleur principal
		$this->db			= new tiFy_Wistify_Reports_Db;
		$this->page 		= 'tify_wistify_reports';
		$this->hook_suffix 	= 'newsletters_page_tify_wistify_reports';
		$this->list_link	=  add_query_arg( array(  'page' => $this->page ), admin_url( 'admin.php' ) );
		$this->status_available = array(			
			'sent'				=> __( 'Envoyé', 'tify' ),
			'deferred'			=> __( 'Différé', 'tify' ),
			'bounced'			=> __( 'Hard Bounced', 'tify' ),
			'rejected'			=> __( 'Rejeté', 'tify' ),
			'soft-bounced'		=> __( 'Soft Bounced', 'tify' ),
			'posted'			=> __( 'Posté', 'tify' ),
		);
		$this->status_description = array(			
			'bounced'			=> __( 
				'(État permanent) Adresse email de destination pour laquelle le message n\'a pu être distribué '.
				'pour l\'une des raisons suivantes : '.
				'<br/>   - L\'adresse email est incorrecte (mal orthographiée)'.
				'<br/>   - Cette adresse email n’existe pas ou plus'
			),
			'rejected'			=> __( 
				'(État temporaire) Adresse email de destination enregistrée en liste noire pour les raisons suivantes :'.				
				'<br/>   - L\'adresse email de destination est déclaré comme un spam'.
				'<br/>   - L\'adresse email de destination est déclaré comme désinscrit pour l\'adresse email d\'expédition'.
				'<br/>   - L\'adresse email a été ajouté automatiquement dans la liste noire (Statut d\'envoi précédent en bounced)'.
				'<br/>   - L\'adresse email a été ajouté manuellement dans la liste noire'
			),
			'soft-bounced'			=> __( 
				'(État temporaire) Adresse email de destination pour laquelle le message n\a pu être distribué '.
				'pour l\'une des raisons suivantes : '.
				'<br/>   - La boîte de réception du destinataire est pleine'.
				'<br/>   - Le serveur de reception du destinataire rencontre un dysfonctionnement'.
				'<br/>   - Un système de filtrage du serveur du destinataire, empêche la reception du message'
			),
			'posted'			=> __( 
				'Le message a été envoyé au serveur d\'envoi mais n\'a pas encore été traité '
			)
		);
				
		// Actions et Filtres Wordpress
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ) );		
		add_action( 'current_screen', array( $this, 'wp_current_screen' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_wistify_report_update', array( $this, 'wp_ajax' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == ADMIN == **/
	/*** === Menu d'administration === ***/
	function wp_admin_menu(){
		add_submenu_page( 'tify_wistify', __( 'Rapport d\'envoi', 'tify' ), __( 'Rapport d\'envoi', 'tify' ), 'manage_options', $this->page, array( $this, 'view_admin' ) );
	}
	
	/*** === Initialisation de l'interface d'administration === ***/
	function wp_current_screen(){
		if( get_current_screen()->id != $this->hook_suffix )
			return;
		// Initialisation de la table  des enregistrements		
		$this->table = new tiFy_Wistify_Reports_List_Table( $this );
	}
	/*** === Mise en file des scripts === ***/
	function wp_admin_enqueue_scripts( $hook_suffix ){		
		// Bypass
		if( $hook_suffix != $this->hook_suffix )
			return;
		
		wp_enqueue_script( 'tify_wistify_report', $this->master->uri .'/js/report.js', array( 'highcharts-core' ), '150503' );
	}	
	/*** === === ***/
	function wp_ajax(){
		if( ! $_POST['report_id'] )
			die(0);
		$report_id = $_POST['report_id'];
			
		check_ajax_referer( "wistify_report_update-". $report_id, '_ajax_nonce' );
		
		if( ! class_exists( 'WP_List_Table' ) )
			require_once( ABSPATH .'wp-admin/includes/class-wp-list-table.php' );
		// Initialisation de la table  des enregistrements		
		$this->table = new tiFy_Wistify_Reports_List_Table( $this );
		
		$this->update_info( $report_id );
		$report = $this->db->get_item_by_id( $report_id );

		echo $this->table->single_row( $report );
		exit;				
	} 
	
	/* = VUES = */
	/** ==  == **/
	function view_admin(){
		$this->table->prepare_items(); 
	?>
		<div class="wrap">
			<h2>
				<?php _e( 'Rapport d\'expédition', 'tify' );?>
			</h2>
			<?php $this->table->views(); ?>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $this->page;?>">
				
				<?php $this->table->search_box( __( 'Recherche de destinataire', 'tify' ), 'wistify_reports' );?>
				<?php $this->table->display();?>
	        </form>
		</div>
	<?php
	}
	
	/* = CONTRÔLEURS = */
	/** == Mise à jour des informations == **/
	function update_info( $report_id ){
		$id = $this->db->get_item_var( $report_id, 'md__id' );
		
		$report = $this->master->Mandrill->messages_info( array( 'id' => $id ) );

		if( ! is_wp_error( $report ) ) :
			$args = array( 'ts', 'sender', 'template', 'subject', 'email', 'tags', 'opens', 'opens_detail', 'clicks', 'clicks_detail', 'state', 'metadata', 'smtp_events', 'resends' );
			$data = array(
				'report_refreshed'	=> current_time( 'timestamp' )
			);
			foreach( $args as $arg )
				if( isset( $report[$arg] ) )
					$data['report_md_'. $arg] = $report[$arg];
			return $this->db->update_item( $report_id, $data );
		else :
			return $report;		
		endif;
	} 
}

/* == GESTION DES DONNEES EN BASE = */
class tiFy_Wistify_Reports_Db extends tiFy_db{
	/* = ARGUMENTS = */
	public	$install = true;
	
	/* = CONSTRUCTEUR = */	
	function __construct( ){		
		// Définition des arguments
		$this->table 		= 'wistify_report';
		$this->col_prefix	= 'report_';
		$this->has_meta		= false;
		$this->cols			= array(
			'id'			=> array(
				'type'			=> 'BIGINT',
				'size'			=> 20,
			),
			'campaign_id'	=> array(
				'type'			=> 'BIGINT',
				'size'			=> 20,
				'unsigned'		=> true,
				'default'		=> 0
			),
			'refreshed'		=> array(
				'type'			=> 'INT',
				'size'			=> 13,
				'default'		=> 0
			),
			'md_ts'			=> array(
				'type'			=> 'INT',
				'size'			=> 13,
				'default'		=> 0
			),
			'md__id'			=> array(				
				'type'			=> 'VARCHAR',
				'size'			=> 32
			),			
			'md_sender'		=> array(
				'type'			=> 'VARCHAR',
				'size'			=> 255
			),
			'md_template'		=> array(
				'type'			=> 'VARCHAR',
				'size'			=> 255
			),
			'md_subject'		=> array(
				'type'			=> 'VARCHAR',
				'size'			=> 255
			),
			'md_email'			=> array(
				'type'			=> 'VARCHAR',
				'size'			=> 255,
				
				'search'		=> true
			),
			'md_tags'			=> array(
				'type'			=> 'LONGTEXT'
			),
			'md_opens'			=> array(
				'type'			=> 'INT',
				'size'			=> 5,
			),
			'md_opens_detail'	=> array(
				'type'			=> 'LONGTEXT'
			),
			'md_clicks'		=> array(
				'type'			=> 'INT',
				'size'			=> 5,
			),
			'md_clicks_detail'	=> array(
				'type'			=> 'INT',
			),
			'md_state'			=> array(
				'type'			=> 'VARCHAR',
				'size'			=> 25,
			),
			'md_metadata'		=> array(
				'type'			=> 'LONGTEXT'
			),
			'md_smtp_events'	=> array(
				'type'			=> 'LONGTEXT'
			),
			'md_resends'		=> array(
				'type'			=> 'LONGTEXT'
			),
			'md_reject_reason'	=> array(
				'type'			=> 'LONGTEXT'
			)
		);
		
		parent::__construct();				
	}	
}

if( ! is_admin() )
	return;
tify_require( 'admin_view' );


/* = LISTE = */
class tiFy_Wistify_Reports_List_Table extends tiFy_AdminView_List_Table {
	/* = ARGUMENTS = */	
	public 	// Contrôleur
			$main;
	
	/* = CONSTRUCTEUR = */	
	public function __construct( tiFy_Wistify_Reports_Main $main ){
		// Définition du controleur principal	
		$this->main = $main;	
		
		// Définition de la classe parente
       	parent::__construct( 
       		array(
            	'singular'  => 'tify_wistify_report',
            	'plural'    => 'tify_wistify_reports',
            	'ajax'      => true,
            	'screen'	=> $this->main->hook_suffix
        	), 
        	$this->main->db 
		);
		
		// Configuration
		$per_page_default = 50;
	}
	
	/* = CONFIGURATION = */
	/** == Définition des status == **/
	function set_status(){
		return 	array( 
			'available' 		=> $this->main->status_available,
			'current'			=> isset( $_REQUEST['state'] ) ? $_REQUEST['state'] : 0,
			'query_args'		=> ! empty( $_REQUEST['campaign_id'] ) ? array( 'state' => '%s', 'campaign_id' => (int) $_REQUEST['campaign_id'] ) : array( 'state' => '%s' ),
			'count_query_args'	=> ! empty( $_REQUEST['campaign_id'] ) ? array( 'state' => '%s', 'campaign_id' => (int) $_REQUEST['campaign_id'] ) : array( 'state' => '%s' )
		);
	}
	
	/** == Traitement de la requête de récupération des items == **/
	public function extra_parse_query_items(){
		$args = array();
		
		$campaign_id = ! empty( $_REQUEST['campaign_id'] ) ? (int) $_REQUEST['campaign_id'] : 0;
		if( $campaign_id )
			$args['campaign_id'] = $campaign_id;
		
		$status	= isset( $_REQUEST['state'] ) ? $_REQUEST['state'] : 0;
		if( $status )
			$args['state'] = $status;
		
		$args['orderby'] = 'md_ts';
		
		return $args;
	} 
			
	/* = ORGANES DE NAVIGATION = */	
	/** == Filtrage avancé  == **/
	protected function extra_tablenav( $which ) {
		// Bypass
		if( !$this->items )
			return;
	?>
		<div class="alignleft actions">
		<?php if ( 'top' == $which ) : ?>
			<label class="screen-reader-text" for="campaign_id"><?php _e( 'Filtre par campagne', 'tify' ); ?></label>
			<?php 
				wistify_campaigns_dropdown( 
					array(
						'show_option_all'	=> __( 'Toutes les campagnes', 'tify' ),
						'selected' 			=> ! empty( $_REQUEST['campaign_id'] ) ? $_REQUEST['campaign_id'] : 0,
						'query_args'		=> array( 'status' => array( 'distributed', 'in-progress' ) )
					)
				); 
				submit_button( __( 'Filtrer', 'tify' ), 'button', 'filter_action', false, array( 'id' => 'campaign-query-submit' ) );?>
		<?php endif;?>
		</div>
	<?php
	}
			
	/* = COLONNES = */
	/** == Définition des colonnes == **/
	public function get_columns() {
		$c = array(
			//'cb'       				=> '<input type="checkbox" />',			
			'report_refreshed' 		=> __( 'Mise à jour', 'tify' ),
			'report_campaign' 		=> __( 'Campagne', 'tify' ),
			'report_md_state' 		=> __( 'Statut', 'tify' ),
			'report_md_email'    	=> __( 'Destinataire', 'tify' ),
			'report_md_sender'  	=> __( 'Expéditeur', 'tify' ),			
			'report_md_subject' 	=> __( 'Sujet', 'tify' ),
			'report_md_opens' 		=> __( 'Ouverture', 'tify' ),
			'report_md_clicks' 		=> __( 'Clics', 'tify' ),
		);	
		return $c;
	}
	
	/** == Définition de l'ordonnancement par colonne == **/
	public function get_sortable_columns() {
		$c = array(	
			'report_opens' 			=> array( 'opens', false ),
			'report_clicks' 			=> array( 'clicks', false )
		);

		return $c;
	}	
	
	/** == Contenu personnalisé : Mise à jour des infos == **/
	function column_report_refreshed( $item ){
		$date =  ( ! $item->report_refreshed ) ? __( 'Jamais', 'tify' ) : date( __( 'd/m/Y à H:i:s', 'tify' ), $item->report_refreshed );
		
		$link = "<a href=\"#\" class=\"report_update\" data-report_id=\"{$item->report_id}\" data-ajax_nonce=\"". wp_create_nonce( "wistify_report_update-". $item->report_id ) ."\">". __( 'Rafraichir', 'tify' ) ."</a>";
		return $date ."<br>". $link;
	}
	
	/** == Contenu personnalisé : Campagne == **/
	function column_report_campaign( $item ){
		$filter_link = add_query_arg( 'campaign_id', $item->report_campaign_id, $this->main->list_link );
		
		return "<a href=\"". esc_url( $filter_link ) ."\">". wistify_campaign_title( $item->report_campaign_id ) ."</a>";
	}
	
	/** == Contenu personnalisé : Etat == **/
	function column_report_md_state( $item ){
		$label = ( isset( $this->main->status_available[$item->report_md_state] ) ) ? $this->main->status_available[$item->report_md_state] : $item->report_md_state;
		$title = ( isset( $this->main->status_description[$item->report_md_state] ) ) ? $this->main->status_description[$item->report_md_state] : $item->report_md_state;
		switch( $item->report_md_state ):
			default :
				$color = 'inherit';
				break;
			case 'sent' :
				$color = 'green';
				break;
			case 'rejected' :	
			case 'bounced' :
				$color = 'red';
				break;
			case 'soft-bounced' :
				$color = 'orange';
				break;			
		endswitch;	
		
		return "<a href=\"#\" title=\"$title\" style=\"color:$color;\">$label</a>";
	}
}