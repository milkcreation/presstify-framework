<?php
class tiFy_Wistify_Subscribers_Main{
	/* = ARGUMENTS = */
	public	// Configuration
			$page,
			$hook_suffix,
			$list_link,
			$edit_link,
			$status_available,
			
			// Controleurs
			$rel_db,
			$db,
			$import,
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Wistify_Master $master ){
		// CONFIGURATION				
		$this->page 		= 'tify_wistify_subscribers';
		$this->hook_suffix 	= 'newsletters_page_tify_wistify_subscribers';
		$this->list_link	=  add_query_arg( array(  'page' => $this->page ), admin_url( 'admin.php' ) );
		$this->edit_link	=  add_query_arg( array(  'page' => $this->page, 'action' => 'edit' ), admin_url( 'admin.php' ) );
		$this->import_link	=  add_query_arg( array(  'page' => $this->page, 'action' => 'import' ), admin_url( 'admin.php' ) );
		$this->status_available = array(
			'registred'		=> __( 'Inscrits', 'tify' ),			
			'unsubscribed'	=> __( 'Désinscrits', 'tify' ),
			'waiting'		=> __( 'En attente', 'tify' )
		); 
		
		// CONTROLEURS
		$this->master 		= $master; // Controleur principal
		$this->rel_db		= new tiFy_Wistify_MailingLists_Relationships_Db;		
		$this->db			= new tiFy_Wistify_Subscribers_Db( $this );
		if( is_admin() )
			$this->csv		= new tiFy_Wistify_Subscribers_Csv( $this );
						
		// ACTIONS ET FILTRES WORDPRESS
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ) );
		add_action( 'current_screen', array( $this, 'wp_current_screen' ), 9 );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == GLOBAL == **/
	/*** === Initialisation globale de Wordpress === ***/
	function wp_init(){	}
	
	/** == ADMIN == **/
	/** == Menu d'administration == **/
	function wp_admin_menu(){
		add_submenu_page( 'tify_wistify', __( 'Abonnés', 'tify' ), __( 'Abonnés', 'tify' ), 'manage_options', $this->page, array( $this, 'view_admin_redirect' ) );
	}
	
	/** == == **/
	function wp_current_screen(){
		if( get_current_screen()->id != $this->hook_suffix )
			return;
		get_current_screen()->set_parentage( preg_replace( '/\/wp-admin\//', '', $_SERVER['REQUEST_URI'] ) );
		parse_str(get_current_screen()->parent_file, $request);
		
		switch( @$request['action'] ) :
			default :
			case 'list' :
				// Initialisation de la table  des enregistrements		
				$this->table = new tiFy_Wistify_Subscribers_List_Table( $this );
				break;
			case 'edit' :
				// Initialisation des scripts
				wp_enqueue_style( 'tify_wistify_subscriber', $this->master->uri .'/css/subscriber.css', array( ), '150406' );
				// Initialisation du formulaire d'édition
				$this->edit_form = new tiFy_Wistify_Subscribers_Edit_Form( $this );
				break;
			case 'import' :
				
				break;
		endswitch;	
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
			case 'import' :
				$this->csv->admin_render();
				break;
		endswitch;
	}
	
	/** == Liste == **/
	function view_admin_list(){
		$this->table->prepare_items(); 
	?>
		<div class="wrap">
			<h2>
				<?php _e( 'Abonnés', 'tify' );?>
				<a class="add-new-h2" href="<?php echo $this->edit_link;?>"><?php _e( 'Ajouter un abonné', 'tify' );?></a>
				<a class="add-new-h2" href="<?php echo $this->import_link;?>"><?php _e( 'Import d\'abonné', 'tify' );?></a>
			</h2>
			<?php $this->table->notifications();?>
			<?php $this->table->views(); ?>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $this->page;?>">
				<input type="hidden" name="status" value="<?php echo ( ! empty( $_REQUEST['status'] ) ? esc_attr( $_REQUEST['status'] ) : 'registred' );?>">
				
				<?php $this->table->search_box( __( 'Recherche d\'abonné' ), 'wistify_subscriber' );?>
				<?php $this->table->display();?>
	        </form>
		</div>
	<?php
	}
	
	/** == Edition == **/
	function view_admin_edit(){
		$this->edit_form->prepare_item();
	?>
		<div class="wrap">
			<h2>
				<?php _e( 'Éditer un abonné', 'tify' );?>
				<a class="add-new-h2" href="<?php echo $this->edit_link;?>"><?php _e( 'Ajouter un autre abonné', 'tify' );?></a>
			</h2>
			<?php $this->edit_form->notifications();?>
			<?php $this->edit_form->display();?>
		</div>
	<?php
	}	
}

class tiFy_Wistify_MailingLists_Relationships_Db extends tiFy_db{
	/* = ARGUMENTS = */
	public	$install = true;
	
	/* = CONSTRUCTEUR = */	
	function __construct( ){		
		// Définition des arguments
		$this->table 		= 'wistify_list_relationships';
		$this->col_prefix	= 'rel_'; 
		$this->has_meta		= false;
		$this->cols			= array(
			'id' 			=> array(
				'type'			=> 'BIGINT',
				'size'			=> 20
			),
			'subscriber_id'	=> array(
				'type'			=> 'BIGINT',
				'size'			=> 20,
				'unsigned'		=> true
			),
			'list_id'	=> array(
				'type'			=> 'BIGINT',
				'size'			=> 20,
				'unsigned'		=> true
			)
		);
		parent::__construct();
	}
	
	/**
	 * Suppression de toutes les relation liste de diffusion/abonnés
	 */
	 function delete_list_subscribers( $list_id ){
		global $wpdb;
	
		return $wpdb->delete( $this->wpdb_table, array( 'rel_list_id' => $list_id ) );
	}
	 
	/** == Ajout d'une relation abonné/liste de diffusion == **/
	function insert_subscriber_for_list( $subscriber_id, $list_id ){
		global $wpdb;
		
		if( ! $wpdb->query( $wpdb->prepare( "SELECT * FROM {$this->wpdb_table} WHERE {$this->wpdb_table}.rel_subscriber_id = %d AND {$this->wpdb_table}.rel_list_id = %d", $subscriber_id, $list_id ) ) )
			return $wpdb->insert( $this->wpdb_table, array( 'rel_list_id' => $list_id, 'rel_subscriber_id' => $subscriber_id ) );
	}
		
	/** == Suppression d'une relation abonné/liste de diffusion == **/
	 function delete_subscriber_for_list( $subscriber_id, $list_id ){
		global $wpdb;
			
		return $wpdb->delete( $this->wpdb_table, array( 'rel_list_id' => $list_id, 'rel_subscriber_id' => $subscriber_id ) );
	}
	
	/** == Suppression de toutes les relation abonné/listes de diffusion == **/
	 function delete_subscriber_lists( $subscriber_id ){
		global $wpdb;
	
		return $wpdb->delete( $this->wpdb_table, array( 'rel_subscriber_id' => $subscriber_id ) );
	} 
}

/* == GESTION DES DONNEES EN BASE = */
class tiFy_Wistify_Subscribers_Db extends tiFy_db{
	/* = ARGUMENTS = */
	public	$install = true,
			$rel_db;
	
	/* = CONSTRUCTEUR = */	
	function __construct( ){		
		// Définition des arguments
		$this->table 		= 'wistify_subscriber';
		$this->col_prefix	= 'subscriber_';
		$this->has_meta		= true;
		$this->cols			= array(
			'id' 			=> array(
				'type'				=> 'BIGINT',
				'size'				=> 20
			),
			'uid' 			=> array(
				'type'				=> 'VARCHAR',
				'size'				=> 32,
				'default'			=> null
			),
			'email'			=> array(
				'type'			=> 'VARCHAR',
				'size'			=> 255,
				
				'search'		=> true
			),
			'date'			=> array(
				'type'			=> 'DATETIME',
				'default'		=> '0000-00-00 00:00:00'
			),
			'modified'		=> array(
				'type'			=> 'DATETIME',
				'default'		=> '0000-00-00 00:00:00'
			),
			'status'		=> array(
				'type'			=> 'VARCHAR',
				'size'			=> 255,
				'default'		=> 'registred',
				
				'any'			=> array( 'registred', 'waiting', 'unsubscribed' )
			)
		);		
		parent::__construct();
		
		$this->rel_db = new tiFy_Wistify_MailingLists_Relationships_Db;				
	}
	
	/* = REQUÊTES = */	
	/** == Compte le nombre d'éléments == **/
	function count_items( $args = array() ){
		global $wpdb;
		// Traitement des arguments
		$defaults = array(
			'include'	=> '',
			'exclude'	=> '',
			'search'	=> '',
			'limit' 	=> -1,
			'list_id'	=> 0
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
										
		// Requête
		$query  = "SELECT COUNT( {$this->wpdb_table}.{$this->primary_key} ) FROM {$this->wpdb_table}";
		// Jointure
		if( $list_id )
			$query .= " INNER JOIN {$this->rel_db->wpdb_table} ON {$this->wpdb_table}.subscriber_id = {$this->rel_db->wpdb_table}.rel_subscriber_id";
				
		/// Conditions
		$query .= " WHERE 1";
		
		//// Relation
		if( $list_id )
			$query .= " AND {$this->rel_db->wpdb_table}.rel_list_id = {$list_id}";
		
		//// Conditions prédéfinies
		$query .= " ". $this->_parse_conditions( $args, $defaults );
		/// Recherche
		if( $this->search_cols && ! empty( $search ) ) :
			$like = '%' . $wpdb->esc_like( $search ) . '%';
			$query .= " AND (";
			foreach( $this->search_cols as $search_col ) :
				$search_query[] = "{$this->wpdb_table}.{$search_col} LIKE '{$like}'";
			endforeach;
			$query .= join( " OR ", $search_query );
			$query .= ")";	
		endif;
		/// Exclusions
		if( $exclude )
			$query .= $this->_parse_exclude( $exclude );
		/// Inclusions
		if( $include )
			$query .= $this->_parse_include( $include );
		
		//// Limite
		if( $limit > -1 )
			$query .= " LIMIT $limit";
		
		// Résultat		
		return $wpdb->get_var( $query );
	}

	/** == Récupération de la valeur de plusieurs éléments == **/
	function get_items_col( $col = null, $args = array() ){
		global $wpdb;
		// Traitement des arguments
		$defaults = array(
			'include'	=> '',
			'exclude'	=> '',
			'search'	=> '',				
			'per_page' 	=> -1,
			'paged' 	=> 1,
			'order' 	=> 'DESC',
			'orderby' 	=> $this->primary_col,
			'list_id'	=> 0
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		$col = ! $col ?  $this->primary_col : $col;
		if( $this->col_prefix_auto )
			$col = $this->col_prefix . $col;
				
		// Requête
		$query  = "SELECT {$this->wpdb_table}.{$col} FROM {$this->wpdb_table}";
		// Jointure
		if( $list_id )
			$query .= " INNER JOIN {$this->rel_db->wpdb_table} ON {$this->wpdb_table}.subscriber_id = {$this->rel_db->wpdb_table}.rel_subscriber_id";
					
		/// Conditions
		$query .= " WHERE 1";
		
		//// Relation
		if( $list_id )
			$query .= " AND {$this->rel_db->wpdb_table}.rel_list_id = {$list_id}";
		
		//// Conditions prédéfinies
		$query .= " ". $this->_parse_conditions( $args, $defaults );
		/// Recherche
		if( $this->search_cols && ! empty( $search ) ) :
			$like = '%' . $wpdb->esc_like( $search ) . '%';
			$query .= " AND (";
			foreach( $this->search_cols as $search_col ) :
				$search_query[] = "{$this->wpdb_table}.{$search_col} LIKE '{$like}'";
			endforeach;
			$query .= join( " OR ", $search_query );
			$query .= ")";	
		endif;
		/// Exclusions
		if( $exclude )
			$query .= $this->_parse_exclude( $exclude );
		/// Inclusions
		if( $include )
			$query .= $this->_parse_include( $include );
		/// Ordre	
		$query .= $this->_parse_order( $orderby, $order );
		/// Limite
		if( $per_page > 0 ) :
			$offset = ($paged-1)*$per_page;
			$query .= " LIMIT {$offset}, {$per_page}";
		endif;
		
		// Resultats				
		if( $res = $wpdb->get_col( $query ) )
			return array_map( 'maybe_unserialize', $res );
	}
		
	/* = REQUETES PERSONNALISÉES = */	
	/** == Vérifie l'existance d'un email pour un abonné == **/
	function email_exists( $email, $exclude_id = null ){
		global $wpdb;
		
		$query = "SELECT COUNT(subscriber_id) FROM {$this->wpdb_table} WHERE 1 AND subscriber_email = %s";
		if( $exclude_id )
			$query .= " AND subscriber_id != %d";
		
		return $wpdb->get_var( $wpdb->prepare( $query, $email, $exclude_id ) );
	}
}

if( ! is_admin() )
	return;
tify_require( 'admin_view' );
	
/* = EDITION = */	
class tiFy_Wistify_Subscribers_Edit_Form extends tiFy_AdminView_Edit_Form{
	public	// Controleur
			$main;
	
	/* = CONSTRUCTEUR = */
	public function __construct( tiFy_Wistify_Subscribers_Main $main ){
		// Controleur
		$this->main = $main;
		$this->list_db = new tiFy_Wistify_MailingLists_Db;
		
		// Configuration
		$args = array(
			'screen' => $this->main->hook_suffix
		);
		
		parent::__construct( $args, $this->main->db );
	}
	
	/* = CONFIGURATION = */
	/** == Préparation de l'object à éditer == **/
	public function prepare_item(){
		// Récupération de l'abonné à éditer
		$this->item = $this->db->get_item_by_id( (int) $_GET['subscriber'] );	
	}
	
	/** == Définition des messages d'erreur == **/
	public function set_errors(){
		return array( 
			1 => __( 'L\'adresse email de l\'abonné doit être renseignée', 'tify' ),
			2 => __( 'Le format de l\'adresse email n\'est pas valide', 'tify' ),
			3 => __( 'Cet email est déjà utilisé pour un autre abonné', 'tify' )
		);
	}
	
	/** Définition des messages informatifs == **/
	public function set_messages(){
		return array( 
			1 => __( 'L\'utilisateur a été enregistré avec succès', 'tify' )
		);
	}
	
	/* = CONTRÔLEURS = */	
	/** == Enregistrement d'un abonné == **/
	function process_actions(){	
		switch( $this->current_action() ) :
			case 'edit' :
				if( ! isset( $_GET['subscriber'] ) ) :					
					$this->item = $this->get_default_item_to_edit( );
					$location = add_query_arg( array( 'subscriber' => $this->item->subscriber_id ), $this->main->edit_link );		
					wp_redirect( $location );	
					exit;
				else :
					$item = $this->db->get_item_by_id( (int) $_GET['subscriber'] );
					if ( ! $item )
						wp_die( __( 'Vous tentez de modifier un abonné qui n’existe pas. Peut-être a-t-il été supprimé ?', 'tify' ) );		
					if ( ! current_user_can( 'edit_posts' ) )
						wp_die( __( 'Vous n’avez pas l’autorisation de modifier cet abonné.', 'tify' ) );	
				endif;		
			break;
			case 'editsubscriber' :	
				$location = remove_query_arg( array( 'action', 'error', 'notice', 'message' ), wp_get_referer() );			
				$location = add_query_arg( array( 'action' => 'edit' ), $location );
				
				$data = $this->translate_data();
				
				if( empty( $data['subscriber_email'] ) ) :
					$location = add_query_arg( array( 'error' => 1 ), $location );
				elseif( ! is_email( $data['subscriber_email'] ) ) :
					$location = add_query_arg( array( 'error' => 2 ), $location );	
				elseif( $this->db->email_exists( $data['subscriber_email'], $data['subscriber_id'] ) ) :
					$location = add_query_arg( array( 'error' => 3 ), $location );
				else :
					// Enregistrement de l'abonné	 
					$subscriber_id = $this->db->insert_item( $data );					
					// Enregistrement des liaisons abonné/liste
					/// Récupération des listes liées à l'abonné
					$original_lists = $this->list_db->get_subscriber_list_ids( $subscriber_id );	
					/// Mise à jour des listes liées à l'abonné
					$update_lists = ! empty( $_REQUEST['subscriber_list'] ) ? $_REQUEST['subscriber_list'] : array() ;				
					//// Suppression des anciennes listes
					foreach( array_diff( $original_lists, $update_lists ) as $list_id )
						$this->main->rel_db->delete_subscriber_for_list( (int) $subscriber_id, (int) $list_id );
					//// Ajout des nouvelles listes
					foreach( (array)$update_lists as $list_id )
						$this->main->rel_db->insert_subscriber_for_list( (int) $subscriber_id, (int) $list_id );
						
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
		if( ! empty( $data['subscriber_id'] ) )
			 $data['subscriber_id'] = (int) $data['subscriber_id'];
		/// Date
		if( empty( $data['subscriber_date'] ) || ( $data['subscriber_date'] === '0000-00-00 00:00:00' ) )
			$data['subscriber_date'] = current_time( 'mysql' );
		/// Date de mise à jour
		$data['subscriber_modified'] = current_time( 'mysql' );
		// Filtrage des données
		foreach( $data as $data_key => $data_value )
			if( ! in_array( $data_key, $this->db->col_names ) )
				unset( $data[$data_key] );
		
		return $data;
	}
	
	/** == Création d'un abonné à éditer == **/
	function get_default_item_to_edit( ){
		if( $item_id = $this->main->db->insert_item( array( 'subscriber_uid' => tify_generate_token(), 'subscriber_status' => 'registred' ) ) )
			return $this->main->db->get_item_by_id( (int) $item_id );
	}	
	
	/* = VUES = */
	/** == Champs cachés == **/
	function hidden_fields(){
		// Définition des actions
		$form_action 	= 'editsubscriber';
		$nonce_action 	= 'update-subscriber_'. $this->item->subscriber_id;
		
		wp_nonce_field( $nonce_action ); ?>
		<input type="hidden" id="user-id" name="user_ID" value="<?php echo get_current_user_id(); ?>" />
		<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ) ?>" />
		<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ) ?>" />				
		<input type="hidden" id="original_item_status" name="original_status" value="<?php echo esc_attr( $this->item->subscriber_status) ?>" />
		<input type="hidden" id="referredby" name="referredby" value="<?php echo esc_url( wp_get_referer() ); ?>" />		
		<input type="hidden" id="subscriber_id" name="subscriber_id" value="<?php echo esc_attr(  $this->item->subscriber_id );?>" />
		<input type="hidden" id="subscriber_date" name="subscriber_date" value="<?php echo esc_attr( $this->item->subscriber_date );?>" />
	<?php
	}
	
	/** == Formulaire d'édition == **/
	function form(){
		$db_list = new tiFy_Wistify_MailingLists_Db;
		$suscriber_list = $this->list_db->get_subscriber_list_ids( $this->item->subscriber_id );
	?>
		<input type="text" id="email" name="subscriber_email" value="<?php echo $this->item->subscriber_email;?>" placeholder="<?php _e( 'Adresse email de l\'abonné', 'tify' );?>">
			
		<h3><?php _e( 'Listes de diffusion', 'tify' );?></h3>
		<ul id="mailing-lists">
		<?php foreach( (array) $db_list->get_items( array( 'orderby' => 'title', 'order' => 'ASC', 'status' => 'publish' ) ) as $l ) : $checked = in_array( $l->list_id, $suscriber_list ) ? true : false; ?>
			<li>
				<label>
					<input type="checkbox" name="subscriber_list[]" value="<?php echo $l->list_id;?>" <?php checked( $checked );?>/>
					<span class="title"><?php echo $l->list_title;?></span>
					<span class="description"><?php echo nl2br( $l->list_description );?></span>
					<span class="numbers"><?php echo $this->db->count_items( array( 'list_id' => $l->list_id, 'status' => 'registred' ) );?></span>
				</label>
			</li>
		<?php endforeach;?>
		</ul>
	<?php
	}

	/** == == **/
	function minor_actions(){
	?>
		<div style="padding:10px 0">
			<label><strong><?php _e( 'Status', 'tify' );?></strong>&nbsp;
				<select name="subscriber_status">
				<?php foreach( $this->main->status_available as $status => $label ) : if( $status === 'trash' ) continue;?>
					<option value="<?php echo $status;?>" <?php selected( $status == $this->item->subscriber_status );?>><?php echo $label;?></option>
				<?php endforeach;?>	
				</select>
			</label>
		</div>
	<?php
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
	        								'subscriber' 		=> $this->item->{$this->db->primary_key}
										),
										admin_url( 'admin.php' ) 
									),
									'wistify_subscriber_trash_'. $this->item->{$this->db->primary_key} 
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
class tiFy_Wistify_Subscribers_List_Table extends tiFy_AdminView_List_Table{
	/* = ARGUMENTS = */	
	public 	// Contrôleur	
			$main,
			$list_db;
		
	/* = CONSTRUCTEUR = */	
	public function __construct( tiFy_Wistify_Subscribers_Main $main ){
		// Définition du controleur principal	
		$this->main = $main;
		$this->list_db = new tiFy_Wistify_MailingLists_Db;
			
		// Définition de la classe parente
       	parent::__construct( array(
            'singular'  => 'tiFy_wistify_subscriber',
            'plural'    => 'tiFy_wistify_subscribers',
            'ajax'      => true,
            'screen'	=> $this->main->hook_suffix
        ), $this->main->db );
		
		// Configuration
		$per_page_default = 50;
		
	}
	
	/* = CONFIGURATION = */
	/** Définition des messages informatifs == **/
	public function set_messages(){
		return array( 
			1 => __( 'L\'abonné a été supprimé avec succès', 'tify' ),
			2 => __( 'L\'abonné a été mis à la corbeille', 'tify' ),
			3 => __( 'L\'abonné a été rétabli', 'tify' )
		);
	}

	/** == Définition des status == **/
	function set_status(){
		return 	array( 
			'available' 		=> array_merge( $this->main->status_available, array( 'trash' => __( 'Corbeille', 'tify' ) ) ),
			'count_query_args'	=> array( 'status' => '%s', 'list_id' => ( ! empty( $_REQUEST['list_id'] ) ? $_REQUEST['list_id'] : 0 ) )
		);
	}
	
	/** == Traitement des requêtes de récupération standard == **/
	public function parse_query_items(){
		// Récupération des arguments
		$status	= isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'any';
		$list_id = isset( $_REQUEST['list_id'] ) ? $_REQUEST['list_id'] : 0;
		$search = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
		$per_page = $this->get_items_per_page( $this->per_page_option, $this->per_page_default );
		$paged = $this->get_pagenum();
		
		// Arguments par défaut
		$args = array(						
			'per_page' 	=> $per_page,
			'paged'		=> $paged,
			'search' 	=> $search,
			'order' 	=> 'DESC',
			'orderby' 	=> 'date',
			'list_id'	=> $list_id
		);
		
		// Traitement des arguments
		if( $status )
			$args['status'] = $status;
		if ( isset( $_REQUEST['orderby'] ) )
			$args['orderby'] = $_REQUEST['orderby'];
		if ( isset( $_REQUEST['order'] ) )
			$args['order'] = $_REQUEST['order'];

		return wp_parse_args( $this->extra_parse_query_items(), $args );
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
			<label class="screen-reader-text" for="list_id"><?php _e( 'Filtre par liste de diffusion', 'tify' ); ?></label>
			<?php 
				wistify_mailing_lists_dropdown( 
					array(
						'show_option_all'	=> __( 'Toutes les listes de diffusion', 'tify' ),
						'selected' 			=> ! empty( $_REQUEST['list_id'] ) ? $_REQUEST['list_id'] : 0
					)
				); 
				submit_button( __( 'Filtrer', 'tify' ), 'button', 'filter_action', false, array( 'id' => 'mailing_list-query-submit' ) );?>
		<?php endif;?>
		</div>
	<?php
	}
			
	/* = COLONNES = */
	/** == Définition des colonnes == **/
	public function get_columns() {
		$c = array(
			'cb'       					=> '<input type="checkbox" />',
			'subscriber_email' 			=> __( 'Email', 'tify' ),
			'subscriber_lists' 			=> __( 'Liste de diffusion', 'tify' ),			
			'subscriber_date' 			=> __( 'Depuis le', 'tify' )
		);	
		return $c;
	}
	
	/** == Définition de l'ordonnancement par colonne == **/
	public function get_sortable_columns() {
		return array(	
			'subscriber_email'  => 'email',
			'subscriber_date'	=> array( 'date', true )
		);
	}	
	
	/** == Contenu personnalisé : Titre == **/
	function column_subscriber_email( $item ){
		$title = ! $item->subscriber_email ? __( '(Pas d\'email)', 'tify' ) : $item->subscriber_email;
		
		if( $item->subscriber_status !== 'trash' ) :
			$actions['edit'] = "<a href=\"".
								add_query_arg(
									array(  
	    								'subscriber' 	=> $item->{$this->db->primary_key}
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
        								'subscriber' 		=> $item->{$this->db->primary_key}, 
        								'_wp_http_referer' 	=> urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) 
									),
									admin_url( 'admin.php' ) 
								),
								'wistify_subscriber_trash_'. $item->{$this->db->primary_key} 
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
        								'subscriber' 		=> $item->{$this->db->primary_key}, 
        								'_wp_http_referer' 	=> urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) 
									),
									admin_url( 'admin.php' ) 
								),
								'wistify_subscriber_untrash_'. $item->{$this->db->primary_key} 
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
        								'subscriber' 		=> $item->{$this->db->primary_key}, 
        								'_wp_http_referer' 	=> urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) 
									),
									admin_url( 'admin.php' ) 
								),
								'wistify_subscriber_delete_'. $item->{$this->db->primary_key} 
							) 
							."\" title=\"". __( 'Supprimer cet item', 'tify' ) ."\">". 
							__( 'Supprimer définitivement', 'tify' ) 
							."</a>";
							
			return sprintf('<strong>%1$s</strong>%2$s', $title, $this->row_actions( $actions ) );				
		endif;     	
	}

	/** == Contenu personnalisé : Listes de diffusion == **/
	function column_subscriber_lists( $item ){
		$output = array();
		if( $list_ids = $this->list_db->get_subscriber_list_ids( $item->{$this->db->primary_key} ) ) : 
			foreach( $list_ids as $list_id )
				$output[] = "<a href=\"". ( add_query_arg( 'list_id', $list_id, $this->main->list_link ) ) ."\">". $this->list_db->get_item_var( $list_id, 'title' ) ."</a>";
			return  join( ', ', $output );
		else :
			return __( 'Aucune', 'tify' );
		endif;			
	}
	
	/** == Contenu personnalisé : Date d'inscription == **/
	function column_subscriber_date( $item ){
		if( $item->subscriber_date !== '0000-00-00 00:00:00' )
			return mysql2date( __( 'd/m/Y à H:i', 'tify' ), $item->subscriber_date );
		else
			return __( 'Indéterminé', 'tify' );
	}
	
	/* = TRAITEMENT DES ACTIONS = */
	function process_bulk_action(){
		if( $this->current_action() ) :
			switch( $this->current_action() ) :
				case 'delete' :
					$item_id = (int) $_GET['subscriber'];			
					check_admin_referer( 'wistify_subscriber_delete_'. $item_id );
					// Suppression de toutes les liaisons listes/abonné
					$this->main->rel_db->delete_subscriber_lists( $item_id );
					// Suppression des métadonnées
					$this->db->delete_item_metadatas( $item_id );
					// Suppression de l'abonné
					$this->db->delete_item( $item_id );
									
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );
					$sendback = add_query_arg( 'message', 1, $sendback );
					
					wp_redirect( $sendback );
					exit;
				break;
				case 'trash' :
					$item_id = (int) $_GET['subscriber'];			
					check_admin_referer( 'wistify_subscriber_trash_'. $item_id );
					// Récupération du statut original de l'abonné et mise en cache
					if( $original_status = $this->db->get_item_var( $item_id, 'status' ) )
						update_metadata( 'wistify_subscriber', $item_id, '_trash_meta_status', $original_status );
					// Modification du status de l'abonné
					$this->db->update_item( $item_id, array( 'subscriber_status' => 'trash' ) );
					
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );
					$sendback = add_query_arg( 'message', 2, $sendback );
					
					wp_redirect( $sendback );
					exit;
				break;
				case 'untrash' :
					$item_id = (int) $_GET['subscriber'];			
					check_admin_referer( 'wistify_subscriber_untrash_'. $item_id );
					// Récupération du statut original de l'abonné et suppression du cache
					$original_status = ( $_original_status = get_metadata( 'wistify_subscriber', $item_id, '_trash_meta_status', true ) ) ? $_original_status : 'waiting';				
					if( $_original_status ) delete_metadata( 'wistify_subscriber', $item_id, '_trash_meta_status' );
					// Récupération du status de l'abonné
					$this->db->update_item( $item_id, array( 'subscriber_status' => $original_status ) );
					
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );
					$sendback = add_query_arg( 'message', 3, $sendback );
					
					wp_redirect( $sendback );
					exit;
				break;	
			endswitch;
		elseif ( ! empty( $_REQUEST['_wp_http_referer'] ) ) :
			wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI']) ) );
	 		exit;
		endif;
	}
}

tify_require( 'csv' );
class tiFy_Wistify_Subscribers_Csv extends tiFy_Csv_Import{
	/* = ARGUMENTS = */
	public	// Référence
			$main;

	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Wistify_Subscribers_Main $main ){
		// Classe de référence
		$this->main = $main;
		
		// Configuration 
		$this->hook_suffix 		= $this->main->hook_suffix;		
		$this->page_title		= __( 'Importation d\'abonnés', 'tify ' );
		$this->table_name 		= 'wistify_subscriber';	
		$this->primary_key		= 'subscriber_id';
		
		// Paramètres du fichier d'example
		$this->sample_filename		= 'import-abonnes-exemple.csv';
		$this->sample_lines		= array(
			array(
				'email', 'lastname', 'firstname', 'company_name', 'function', 'phone_number', 'metadonnée personnalisée', '...'
			),
			array(
				'johndoe@wistify.com', 'Doe', 'John', 'My John Doe Company', 'Director' , '000.00.00.00', 'metadonnée personnalisée', '...'
			),
			array(
				'Adresse email de l\'abonné (requise)', 'Nom de l\'abonné', 'Prénom de l\'abonné', 'Société/Organisation de l\'abonné', 'metadonnée personnalisée', '...'
			)
		);
				
		// Paramètres d'import CSV
		$this->delimiter = ";";		
		$this->offset = 1;
		$this->limit = 10;
		
		// Paramètres d'import des données
		$this->columns_map 		= array( 'subscriber_email' => array( 'email', 'emails' ) );
		$this->columns_update	= array( 'subscriber_email' );
		
		// Actions et Filtres Wordpress
		parent::__construct();
	}
	
	/* = CONTROLEUR = */
	/*** === Vérification d'intégrité de la valeur d'une donnée == **/
	function is_col_data_error( $col_data, $col = null ){
		switch( $col ) :
			case 'subscriber_email' :
				global $wpdb;
				if( empty( $col_data ) ) :
					return __( 'L\'adresse email doit être renseignée', 'tify' );
				elseif( ! is_email( trim( $col_data ) ) ) :
					return __( 'Le format de l\'email est invalide', 'tify' );
				endif;	
				break;
		endswitch;				
	}
	
	/** == Traitement de la valeur d'une donnée == **/
	function parse_col_data( $col_data, $col = null ){
		return trim( $col_data );
	}
	
	/*** === Affichage des options d'import == **/
	function display_import_options(){
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><?php _e( 'Liste de diffusion', 'tify' );?></th>
				<td><?php wistify_mailing_lists_dropdown( 
							array(
								'show_option_none' 	=> __( 'Aucune', 'tify' ),
							)
						);?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
	}
	
	/** == Traitement des données avant insertion == **/
	function parse_row_defaults( $row_datas ){
		if( ! isset( $row_datas['subscriber_uid'] ) )
			$row_datas['subscriber_uid'] = tify_generate_token();
		if( ! isset( $row_datas['subscriber_date'] ) )
			$row_datas['subscriber_date'] = current_time( 'mysql' );
		
		return $row_datas;
	}
	
	/** == Post-traitement de l'import de données d'une ligne == **/
	public function post_row_import( $item_id ){
		// Relation Abonné / Liste de diffusion
		if( ! empty( $_POST['options']['list_id'] ) && ( $_POST['options']['list_id'] != -1 ) ) :			
			$rel_db		= new tiFy_Wistify_MailingLists_Relationships_Db;
			$list_id 	= $_POST['options']['list_id'];
			
			$rel_db->insert_subscriber_for_list( $item_id, $list_id );
		endif;
		
		return $item_id;
	}		
}