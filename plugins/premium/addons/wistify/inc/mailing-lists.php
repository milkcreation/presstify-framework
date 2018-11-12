<?php
class tiFy_Wistify_MailingLists_Main{
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Wistify_Master $master ){
		// DEFINITION DES ARGUMENTS
		$this->master 		= $master; // Controleur principal
		$this->db			= new tiFy_Wistify_MailingLists_Db;
		$this->rel_db		= new tiFy_Wistify_MailingLists_Relationships_Db;
		$this->page 		= 'tify_wistify_mailing_lists';
		$this->hook_suffix 	= 'newsletters_page_tify_wistify_mailing_lists';
		$this->list_link	=  add_query_arg( array(  'page' => $this->page ), admin_url( 'admin.php' ) );
		$this->edit_link	=  add_query_arg( array(  'page' => $this->page, 'action' => 'edit' ), admin_url( 'admin.php' ) );
		$this->status_available = array(
			'publish'		=> __( 'Publiée', 'tify' )
		); 
		
		// ACTIONS ET FILTRES WORDPRESS
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ) );		
		add_action( 'current_screen', array( $this, 'wp_current_screen' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_enqueue_scripts' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == GLOBAL == **/
	/*** === Initialisation globale de Wordpress === ***/
	function wp_init(){	}
	
	/** == ADMIN == **/
	/** === Menu d'administration === **/
	function wp_admin_menu(){
		add_submenu_page( 'tify_wistify', __( 'Listes de diffusion', 'tify' ), __( 'Listes de diffusion', 'tify' ), 'manage_options', $this->page, array( $this, 'view_admin_redirect' ) );
	}
	
	/** === Initialisation de l'interface d'administration === **/
	function wp_current_screen(){
		if( get_current_screen()->id != $this->hook_suffix )
			return;
		// Initialisation de la table  des enregistrements		
		$this->table = new tiFy_Wistify_MailingLists_List_Table( $this );
		// Initialisation du formulaire d'édition
		$this->edit_form = new tiFy_Wistify_MailingLists_Edit_Form( $this );
	}
	
	/** === Mise en file des scripts === ***/
	function wp_admin_enqueue_scripts( $hook_suffix ){
		// Bypass
		if( $hook_suffix != $this->hook_suffix )
			return;
		
		tify_controls_enqueue( 'text_remaining' );
		wp_enqueue_style( 'tify_wistify_mailing_list', $this->master->uri .'/css/mailing-list.css', array( ), '150406' );	
	}
		
	/* = VUES = */
	/** == Redirection == **/
	function view_admin_redirect(){
		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'list';
		switch( $action ) :
			default :
			case 'list' :
				$this->view_admin_list();
				break;
			case 'edit' :
				$this->view_admin_edit();
				break;
		endswitch;
	}
	/** == Liste == **/
	function view_admin_list(){
		$this->table->prepare_items(); 
	?>
		<div class="wrap">
			<h2>
				<?php _e( 'Listes de diffusion', 'tify' );?>
				<a class="add-new-h2" href="<?php echo $this->edit_link;?>"><?php _e( 'Ajouter une liste de diffusion', 'tify' );?></a>
			</h2>
			<?php $this->table->notifications();?>
			<?php $this->table->views(); ?>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $this->page;?>">
				<input type="hidden" name="status" value="<?php echo ( ! empty( $_REQUEST['status'] ) ? esc_attr( $_REQUEST['status'] ) : 'any' );?>">
				
				<?php $this->table->search_box( __( 'Recherche de liste de diffusion' ), 'wistify_mailing_list' );?>
				<?php $this->table->display();?>
	        </form>
		</div>
	<?php
	}
	/** == Edition == **/
	function view_admin_edit(){
		$this->edit_form->prepare_item();		
	?>
		<div id="wistify_mailing_list-edit" class="wrap">
			<h2>
				<?php _e( 'Éditer la liste diffusion', 'tify' );?>
				<a class="add-new-h2" href="<?php echo $this->edit_link;?>"><?php _e( 'Ajouter une liste de diffusion', 'tify' );?></a>
			</h2>
			<?php $this->edit_form->notifications();?>
			
			<?php $this->edit_form->display();?>
		</div>
	<?php
	}
}

/* == GESTION DES DONNEES EN BASE = */
class tiFy_Wistify_MailingLists_Db extends tiFy_db{
	/* = ARGUMENTS = */
	public	$install = true,
			$rel_db;
	
	/* = CONSTRUCTEUR = */	
	function __construct( ){		
		// Définition des arguments
		$this->table 		= 'wistify_list';
		$this->col_prefix	= 'list_'; 
		$this->primary_key	= 'list_id';
		$this->has_meta		= false;
		$this->cols			= array(
			'id' 			=> array(
				'type'			=> 'BIGINT',
				'size'			=> 20
			),
			'title'			=> array(
				'type'			=> 'VARCHAR',
				'size'			=> 255,
				
				'search'		=> true
			),
			'description'	=> array(
				'type'			=> 'LONGTEXT',
				
				'search'		=> true
			),
			'date'		=> array(
				'type'			=> 'DATETIME',
				'default'		=> '0000-00-00 00:00:00'
			),
			'modified'		=> array(
				'type'			=> 'DATETIME',
				'default'		=> '0000-00-00 00:00:00'
			),
			'status'		=> array(
				'type'			=> 'VARCHAR',
				'size'			=> 20,
				'default'		=> 'publish',
				
				'any'			=> 'publish'
			),
			'menu_order'		=> array(
				'type'			=> 'BIGINT',
				'size'			=> 20,
				'default'		=> 0
			),
			'public'		=> array(
				'type'			=> 'TINYINT',
				'size'			=> 1,
				'default'		=> 1
			)
		);		
		parent::__construct();	
		
		$this->rel_db = new tiFy_Wistify_MailingLists_Relationships_Db;		
	}
	
	/** == Vérifie si un intitulé est déjà utilisé pour une liste de diffusion == **/
	function title_exists( $title, $list_id = null ){
		global $wpdb;
		
		$query = "SELECT COUNT(list_id) FROM {$this->wpdb_table} WHERE 1 AND list_title = %s";
		if( $list_id )
			$query .= " AND list_id != %d";
		
		return $wpdb->get_var( $wpdb->prepare( $query, $title, $list_id ) );
	}
	
	/* = REQUETES PERSONNALISÉES = */
	/** == Récupére les listes de diffusion d'un abonné == **/
	function get_subscriber_list_ids( $subscriber_id ){
		global $wpdb;
		
		return $wpdb->get_col( $wpdb->prepare( "SELECT rel_list_id FROM {$this->rel_db->wpdb_table} INNER JOIN {$this->wpdb_table} ON ( {$this->rel_db->wpdb_table}.rel_list_id = {$this->wpdb_table}.list_id ) WHERE {$this->rel_db->wpdb_table}.rel_subscriber_id = %d", $subscriber_id ) );
	}	
}

if( ! is_admin() )
	return;
tify_require( 'admin_view' );

/* = EDITION = */
class tiFy_Wistify_MailingLists_Edit_Form extends tiFy_AdminView_Edit_Form{
	public	// Controleur
			$main;
	
	/* = CONSTRUCTEUR = */
	public function __construct( tiFy_Wistify_Mailinglists_Main $main ){
		// Controleur
		$this->main = $main;
		// Configuration
		$args = array(
			'screen' => $this->main->hook_suffix
		);
		
		parent::__construct( $args, $this->main->db );
	}
	
	/* = CONFIGURATION = */
	/** == Préparation de l'object à éditer == **/
	public function prepare_item(){
		$this->item = $this->main->db->get_item_by_id( (int) $_GET['mailing-list'] );	
	}
	
	/** == Définition des messages d'erreur == **/
	public function set_errors(){
		return array( 
			1 => __( 'Il y a déjà une autre liste de diffusion pourtant le même nom', 'tify' )
		);
	}
	
	/** Définition des messages informatifs == **/
	public function set_messages(){
		return array( 
			1 => __( 'La liste de diffusion a été enregistrée avec succès', 'tify' )
		);
	}
	
	/* = CONTRÔLEURS = */	
	/** == Enregistrement d'un abonné == **/
	function process_actions(){
		//Bypass
		if( get_current_screen()->id != $this->main->hook_suffix )
			return;
		if( ! isset( $_REQUEST['page'] ) && ( $_REQUEST['page'] != $this->page ) )
			return;
		if( ! isset( $_REQUEST['action'] ) )
			return;
		
		switch( $_REQUEST['action'] ) :
			case 'edit' :
				if( ! isset( $_GET['mailing-list'] ) ) :					
					$item = $this->get_default_item_to_edit( );
					$location = add_query_arg( array( 'mailing-list' => $item->list_id ), $this->main->edit_link );		
					wp_redirect( $location );	
					exit;
				else :
					$item = $this->main->db->get_item_by_id( (int) $_GET['mailing-list'] );
					if ( ! $item )
						wp_die( __( 'Vous tentez de modifier un contenu qui n’existe pas. Peut-être a-t-il été supprimé ?', 'tify' ) );		
					if ( ! current_user_can( 'edit_posts' ) )
						wp_die( __( 'Vous n’avez pas l’autorisation de modifier ce contenu.', 'tify' ) );	
				endif;		
			break;
			case 'editmailinglist' :
				$location = remove_query_arg( array( 'action', 'error', 'notice', 'message' ), wp_get_referer() );	
				$location = add_query_arg( array( 'action' => 'edit' ), $location );
			
				$data = $this->translate_data();
				if( $this->main->db->title_exists( $data['list_title'], $data['list_id'] ) ) :
					$location = add_query_arg( array( 'error' => 1 ), $location );
				else :
					$this->main->db->insert_item( $data );
					$location = add_query_arg( array( 'message' => 1 ), $location );				
				endif;				
				
				wp_redirect( $location );
				exit;
				break;
		endswitch;				
	}
	
	/** == Translation des données de formulaire == **/
	function translate_data( $data = null ){
		if ( empty( $data ) )
			$data = $_POST;
		// Translation des valeurs
		/// Identifiant
		if( ! empty( $data['list_id'] ) )
			 $data['list_id'] = (int) $data['list_id'];
		/// Date
		if( empty( $data['list_date'] ) || ( $data['list_date'] === '0000-00-00 00:00:00' ) )
			$data['list_date'] = current_time( 'mysql' );
		/// Date de mise à jour
		$data['list_modified'] = current_time( 'mysql' );	 
		/// Status
		if( ! empty( $data['list_title'] ) && ( $data['list_status'] === 'auto-draft' ) )
			 $data['list_status'] = 'publish';	 
		// Filtrage des données
		foreach( $data as $data_key => $data_value )
			if( ! in_array( $data_key, $this->main->db->col_names ) )
				unset( $data[$data_key] );
		
		return $data;
	}
	
	/** == Création d'une liste de diffusion à éditer == **/
	function get_default_item_to_edit( ){
		if( $item_id = $this->main->db->insert_item( array( 'list_status' => 'auto-draft' ) ) )
			return $this->main->db->get_item_by_id( (int) $item_id );
	}
	
	/* = VUES = */
	/** == Champs cachés == **/
	function hidden_fields(){
		// Définition des actions
		$form_action 	= 'editmailinglist';
		$nonce_action 	= 'update-mailing_list_' . $this->item->list_id;
		
		wp_nonce_field( $nonce_action ); ?>
		<input type="hidden" id="user-id" name="user_ID" value="<?php echo get_current_user_id();?>" />
		<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action );?>" />
		<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action );?>" />				
		<input type="hidden" id="original_item_status" name="original_status" value="<?php echo esc_attr( $this->item->list_status );?>" />
		<input type="hidden" id="referredby" name="referredby" value="<?php echo esc_url( wp_get_referer() ); ?>" />
		
		<input type="hidden" id="list_id" name="list_id" value="<?php echo esc_attr( $this->item->list_id );?>" />
		<input type="hidden" id="list_date" name="list_date" value="<?php echo esc_attr( $this->item->list_date );?>" />
		<input type="hidden" id="list_status" name="list_status" value="<?php echo esc_attr( $this->item->list_status );?>" />
	<?php
	}
	
	/** == Formulaire d'édition == **/
	function form(){
	?>
		<input type="text" id="title" name="list_title" value="<?php echo $this->item->list_title;?>" placeholder="<?php _e( 'Intitulé de la liste de diffusion', 'tify' );?>">						
	<?php tify_control_text_remaining( array( 'id' => 'content', 'name' => 'list_description', 'value' => $this->item->list_description, 'attrs' => array( 'placeholder' => __( 'Brève description de la liste de diffusion', 'tify' ) ) ) );
	}
	
	/** == == **/
	function major_actions(){
	?>
		<div class="deleting">			
			<a href="<?php echo wp_nonce_url( 
		        					add_query_arg( 
	        							array( 
	        								'page' 				=> $_REQUEST['page'], 
	        								'action' 			=> 'trash', 
	        								'mailing_list' 		=> $this->item->{$this->db->primary_key}
										),
										admin_url( 'admin.php' ) 
									),
									'wistify_mailing_list_trash_'. $this->item->{$this->db->primary_key} 
							);?>" title="<?php _e( 'Mise à la corbeille de l\'élément', 'tify' );?>">
				<?php _e( 'Déplacer dans la corbeille', 'tify' );?>
			</a>
		</div>
		<div class="publishing">
			<?php submit_button( __( 'Sauver les modifications', 'tify' ), 'primary', 'submit', false ); ?>
		</div>
	<?php
	}
}

/* = LISTE = */
class tiFy_Wistify_MailingLists_List_Table extends tiFy_AdminView_List_Table {
	/* = ARGUMENTS = */	
	public 	// Contrôleur
			$main;
	
	/* = CONSTRUCTEUR = */	
	public function __construct( tiFy_Wistify_Mailinglists_Main $main ){
		// Définition du controleur principal	
		$this->main = $main;	
			
		// Définition de la classe parente
       	parent::__construct( array(
            'singular'  => 'tify_wistify_mailing_lists',
            'plural'    => 'tify_wistify_mailing_lists',
            'ajax'      => true,
            'screen'	=> $this->main->hook_suffix
        ), $this->main->db );
	}
	
	/* = CONFIGURATION = */
	/** Définition des messages informatifs == **/
	public function set_messages(){
		return array( 
			1 => __( 'La liste de diffusion a été supprimé avec succès', 'tify' ),
			2 => __( 'La liste de diffusion a été mise à la corbeille', 'tify' ),
			3 => __( 'La liste de diffusion a été rétablie', 'tify' )
		);
	}
	/** == Définition des status == **/
	function set_status(){
		return 	array( 
			'available' 		=> array( 'trash' => __( 'Corbeille', 'tify' ) ),
			'current'			=> isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'any',
		);
	}

	/** == Traitement de la requête de récupération des items == **/
	public function extra_parse_query_items(){
		$args = array();
				
		$args['status'] = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'any';
					
		return $args;
	} 
				
	/* = COLONNES = */
	/** == Définition des colonnes == **/
	public function get_columns() {
		$c = array(
			'cb'       				=> '<input type="checkbox" />',
			'list_title' 			=> __( 'Intitulé', 'tify' ),
			'list_content'  		=> __( 'Description', 'tify' ),
			'subscribers_number'    => __( 'Nombre d\'abonnés', 'tify' ),
			'list_date' 			=> __( 'Date', 'tify' )
		);	
		return $c;
	}
	
	/** == Définition de l'ordonnancement par colonne == **/
	public function get_sortable_columns() {
		return array(	
			'list_title'  => 'title'
		);
	}	

	/** == Contenu personnalisé : Titre == **/
	function column_list_title( $item ){
		$title = ! $item->list_title ? __( '(Pas de titre)', 'tify' ) : $item->list_title;		
		
		if( $item->list_status !== 'trash' ) :
			$actions['edit'] = "<a href=\"".
								add_query_arg(
									array(  
	    								'mailing-list' 	=> $item->{$this->main->db->primary_key}
									),
									$this->main->edit_link 
								)
								."\" title=\"". __( 'Éditer cet item', 'tify' ) ."\">". 
								__( 'Éditer', 'tify' ) 
								."</a>";
			$actions['trash'] = "<a href=\"". 
	        					wp_nonce_url( 
	        						add_query_arg( 
	        							array( 
	        								'page' 				=> $_REQUEST['page'], 
	        								'action' 			=> 'trash', 
	        								'mailing_list' 		=> $item->{$this->main->db->primary_key}, 
	        								'_wp_http_referer' 	=> urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) 
										),
										admin_url( 'admin.php' ) 
									),
									'wistify_mailing_list_trash_'. $item->{$this->main->db->primary_key}
								) 
								."\" title=\"". __( 'Mise à la corbeille de l\'élément', 'tify' ) ."\">". 
								__( 'Mettre à la corbeille', 'tify' ) 
								."</a>";
								
			return sprintf('<a href="#">%1$s</a>%2$s', $title, $this->row_actions( $actions ) );							
		else :
		   $actions['untrash'] = "<a href=\"". 
	        					wp_nonce_url( 
	        						add_query_arg( 
	        							array( 
	        								'page' 				=> $_REQUEST['page'], 
	        								'action' 			=> 'untrash', 
	        								'mailing_list' 		=> $item->{$this->main->db->primary_key}, 
	        								'_wp_http_referer' 	=> urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) 
										),
										admin_url( 'admin.php' ) 
									),
									'wistify_mailing_list_untrash_'. $item->{$this->main->db->primary_key}
								) 
								."\" title=\"". __( 'Rétablissement de l\'élément', 'tify' ) ."\">". 
								__( 'Rétablir', 'tify' ) 
								."</a>";
	        $actions['delete'] = "<a href=\"". 
	        					wp_nonce_url( 
	        						add_query_arg( 
	        							array( 
	        								'page' 				=> $_REQUEST['page'], 
	        								'action' 			=> 'delete', 
	        								'mailing_list' 		=> $item->{$this->main->db->primary_key}, 
	        								'_wp_http_referer' 	=> urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) 
										),
										admin_url( 'admin.php' ) 
									),
									'wistify_mailing_list_delete_'. $item->{$this->main->db->primary_key}
								) 
								."\" title=\"". __( 'Supprimer cet item', 'tify' ) ."\">". 
								__( 'Supprimer définitivement', 'tify' ) 
								."</a>";
								
			return sprintf('<strong>%1$s</strong>%2$s', $title, $this->row_actions( $actions ) );					
		endif;		
	}

	/** == Contenu personnalisé : Nombre d'abonnés == **/
	function column_subscribers_number( $item ){
		$subscribers_query = new tiFy_Wistify_Subscribers_Db;
		
		$registred = (int) $subscribers_query->count_items( array( 'list_id' => $item->list_id, 'status' => 'registred' ) );
		$unsubscribed = (int) $subscribers_query->count_items( array( 'list_id' => $item->list_id, 'status' => 'unsubscribed' ) );
		$waiting = (int) $subscribers_query->count_items( array( 'list_id' => $item->list_id, 'status' => 'waiting' ) );
		$trashed = (int) $subscribers_query->count_items( array( 'list_id' => $item->list_id, 'status' => 'trash' ) );
			
		$total = $subscribers_query->count_items( array( 'list_id' => $item->list_id ) );
		
		$output = "<strong style=\"text-transform:uppercase\">". sprintf( _n( '%d abonné au total', '%d abonnés au total', ( $total <= 1 ), 'tify' ), $total ) ."</strong>";
		$output .= "<br><em style=\"color:#999; font-size:0.9em;\">". sprintf( _n( '%d inscrit', '%d inscrits', ( $registred <= 1 ), 'tify' ), $registred ) .", </em>";
		$output .= "<em style=\"color:#999; font-size:0.9em;\">". sprintf( _n( '%d désinscrit', '%d désinscrits', ( $unsubscribed <= 1 ), 'tify' ), $unsubscribed ) .", </em>";
		$output .= "<em style=\"color:#999; font-size:0.9em;\">". sprintf( _n( '%d en attente', '%d en attente', ( $waiting <= 1 ), 'tify' ), $waiting ) .", </em>";
		$output .= "<em style=\"color:#999; font-size:0.9em;\">". sprintf( _n( '%d à la corbeille', '%d à la corbeille', ( $trashed <= 1 ), 'tify' ), $trashed ) ."</em>";
		
		return $output;
	}
	
	/** == Contenu personnalisé : Date de création de la liste == **/
	function column_list_date( $item ){
		if( $item->list_date !== '0000-00-00 00:00:00' )
			return mysql2date( __( 'd/m/Y à H:i', 'tify' ), $item->list_date );
		else
			return __( 'Indéterminé', 'tify' );
	}
	
	/* = TRAITEMENT DES ACTIONS = */
	function process_bulk_action(){
		if( $this->current_action() ) :
			switch( $this->current_action() ) :
				case 'delete' :
					$item_id = (int) $_GET['mailing_list'];			
					check_admin_referer( 'wistify_mailing_list_delete_'. $item_id );
					// Destruction des liaisons abonnés/liste
					$this->main->rel_db->delete_list_subscribers( $item_id );
					// Suppression de la liste de diffusion
					$this->main->db->delete_item( $item_id );
					
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );
					$sendback = add_query_arg( 'message', 1, $sendback );
	
					wp_redirect( $sendback );
					exit;
				break;
				case 'trash' :
					$item_id = (int) $_GET['mailing_list'];			
					check_admin_referer( 'wistify_mailing_list_trash_'. $item_id );
					$this->main->db->update_item( $item_id, array( 'list_status' => 'trash' ) );
					
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );
					$sendback = add_query_arg( 'message', 2, $sendback );
					
					wp_redirect( $sendback );
				break;
				case 'untrash' :
					$item_id = (int) $_GET['mailing_list'];			
					check_admin_referer( 'wistify_mailing_list_untrash_'. $item_id );
					$this->main->db->update_item( $item_id, array( 'list_status' => 'publish' ) );
					
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );
					$sendback = add_query_arg( 'message', 3, $sendback );
					
					wp_redirect( $sendback );
				break;		
			endswitch;
		elseif ( ! empty( $_REQUEST['_wp_http_referer'] ) ) :
			wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI']) ) );
	 		exit;
		endif;	
	}
}