<?php
class tiFy_Forum_Contributors{
	/* = ARGUMENTS = */
	public	// Configuration
			$page_slug,
			$hookname,
			
			// Paramètres
			$action,
			$list_table,
			$edit_form,
			$list_link,
			$edit_link,
			
			// Référence
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Forum $master ){
		// Instanciation de la classe de référence
		$this->master = $master;
		
		// Configuration
		$this->page_slug 	= 'tify_forum_contributors';
		$this->hookname 	= 'forums_page_tify_forum_contributors';
		
		// Paramètres
		$this->action		= isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'list';
		$this->edit_link	= add_query_arg( array( 'action' => 'edit', 'page' => $this->page_slug ), admin_url( '/'. $this->master->menu_slug ) );
						
		// Actions et Filtres Wordpress
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ) );
		add_action( 'current_screen', array( $this, 'wp_current_screen' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Menu d'administration == **/
	function wp_admin_menu(){
		add_submenu_page( $this->master->menu_slug, __( 'Contributeurs', 'tify' ), __( 'Contributeurs', 'tify' ), 'edit_users', $this->page_slug, array( $this, 'view_admin_redirect' ) );
	}
	
	/** == Définition de la page courante == **/
	function wp_current_screen(){
		switch( $this->action ) :
			default :
			case 'list' :		
				$this->list_table = new tiFy_Forum_Contributors_ListTable( $this );
				break;
			case 'edit' :
				$this->edit_form = new tiFy_Forum_Contributors_EditForm( $this );
				break;
		endswitch;
	}
	
	/* = CONTROLEURS = */
	/** == Vérifie si l'utilisateur courant à un compte accès pro. == */
	function has_account( $user_id = 0 ){
		if( ! $user_id )
			$user_id = get_current_user_id();
		if( ! $user_id )
			return false;
		if( in_array( get_user_role( $user_id ), array_keys( $this->master->roles ) )  )
			return true;
		
		return false;
	}
	
	/* = VUES = */
	/** == ADMIN == **/
	/*** === Redirection des templates backoffice === ***/
	function view_admin_redirect(){
		switch( $this->action ) :
			default :
			case 'list' :		
				$this->view_admin_list();
				break;
			case 'edit' :
				$this->view_admin_edit();
				break;
		endswitch;
	}
	
	/*** === Liste des contributeurs === ***/
	function view_admin_list(){
		$this->list_table->prepare_items(); 
	?>
		<div class="wrap">
			<h2>
				<?php _e( 'Listes des contributeurs', 'tify' );?>
				<a class="add-new-h2" href="<?php echo $this->edit_link;?>"><?php _e( 'Ajouter un client', 'tify' );?></a>
			</h2>
			
			<?php $this->list_table->views(); ?>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $this->page_slug;?>">
				<input type="hidden" name="status" value="<?php echo ( ! empty( $_REQUEST['status'] ) ? esc_attr( $_REQUEST['status'] ) : '' );?>">
				
				<?php $this->list_table->search_box( __( 'Recherche de contributeur', 'tify' ), 'tify_forum_contributor' );?>
				<?php $this->list_table->display();?>
	        </form>
		</div>
	<?php
	}
	
	/*** === Edition d'un contributeur === ***/
	function view_admin_edit(){
		$this->edit_form->prepare_item();
	?>
		<div class="wrap">
			<h2>
				<?php _e( 'Editer un client', 'tify' );?>
				<a class="add-new-h2" href="<?php echo $this->edit_link;?>"><?php _e( 'Ajouter un client', 'tify' );?></a>
			</h2>
			<?php $this->edit_form->notifications();?>
						
			<form <?php $this->edit_form->form_attrs();?>>
				<?php $this->edit_form->display();?>
			</form>
		</div>
	<?php
	}
}

if( ! is_admin() )
	return;
tify_require( 'admin_view' );

/* = LISTE = */
class tiFy_Forum_Contributors_ListTable extends tiFy_AdminView_List_Table {
	/* = ARGUMENTS = */
	public 	// Configuration 
			$roles,
			// Référence
			$main;
	
	/* = CONSTRUCTEUR = */	
	public function __construct( tiFy_Forum_Contributors $main ){
		// Instanciation de la classe de référence
		$this->main = $main;
		
		// Configuration
		$this->roles = array_keys( $this->main->master->roles );
		
		// Définition de la classe parente
       	parent::__construct( array(
	            'singular'  => 'tify_forum_contributor',
	            'plural'    => 'tify_forum_contributors',
	            'ajax'      => true,
	            'screen'	=> $this->main->hookname
        	) 
		);
	}
	
	/* = CONFIGURATION = */
	/** == Définition des status == **/
	function set_status(){
		return 	array( 
			'available' 		=> array( 1 => __( 'Actif', 'tify' ), 0 => __( 'Inactif', 'tify' ), -1 => __( 'En attente', 'tify' )  ),
			'location'			=> $this->main->list_link		
		);
	}
	
	/* = PREPARATION DES ITEMS = */
	public function prepare_items(){
		global $wpdb;
		// Récupération des arguments
		$search = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';	
		$per_page = $this->get_items_per_page( $this->per_page_option, $this->per_page_default );
		$paged = $this->get_pagenum();
		$roles = ! empty( $_REQUEST['role'] ) ? array( $_REQUEST['role'] ) : $this->roles;		
		
		// Arguments par défaut
		$args = array(						
			'number' 		=> $per_page,
			'offset' 		=> ( $paged-1 ) * $per_page,
			'count_total'	=> true,
			'search' 		=> $search,
			'fields' 		=> 'all_with_meta',
			'orderby'		=> 'user_registered',
			'order'			=> 'DESC',
			'meta_query'	=> array(
				array(
			    	'key' => $wpdb->get_blog_prefix( get_current_blog_id() ) . 'capabilities',
			    	'value' => '"(' . implode('|', array_map( 'preg_quote', $roles ) ) . ')"',
			    	'compare' => 'REGEXP'
				)
			)
		);
		
		// Traitement des arguments
		if ( '' !== $args['search'] )
			$args['search'] = '*' . $args['search'] . '*';			
		if ( isset( $_REQUEST['orderby'] ) )
			$args['orderby'] = $_REQUEST['orderby'];
		if ( isset( $_REQUEST['order'] ) )
			$args['order'] = $_REQUEST['order'];
		if ( ! empty( $_REQUEST['status'] ) && $_REQUEST['status'] !== 'any' ) :
			$args['meta_query']['relation'] = 'AND'; 
			$args['meta_query'][] = array(
				'key' 		=> $wpdb->get_blog_prefix( ) .'tify_membership_status',
				'value'		=> (int) $_REQUEST['status'],
				'type'		=> 'NUMERIC'
			);
		endif;
		
		// Récupération des items
		$wp_user_query = new WP_User_Query( $args );
        $this->items = $wp_user_query->get_results();
		
		// Pagination
		$total_items = $wp_user_query->get_total();
		$this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                    
            'total_pages' => ceil( $total_items / $per_page )
			) 
		);
		
		return wp_parse_args( $this->extra_parse_query_items(), $args );
	}

	/* = ORGANES DE NAVIGATION = */
	/** == Filtrage principal  == **/
	public function get_views(){
		// Bypass
		if( ! $_status = $this->_get_status() )
			return;
	
		global $wpdb;
		$count_query_args = array(	
			'orderby'		=> 'ID',
			'order'			=> 'DESC',
			'meta_query' 	=> 
				array(
					array(
				    	'key' => $wpdb->get_blog_prefix( ) . 'capabilities',
				    	'value' => '"(' . implode('|', array_map( 'preg_quote', $this->roles ) ) . ')"',
				    	'compare' => 'REGEXP'
					)
				)
		);		
		
		$views = array();
		
		if( $_status['show_all'] ) :
			$query_args = array();
			foreach( (array) $_status['query_args'] as $args => $value ) :
				$query_args[$args] = sprintf( $value, 'any' );
			endforeach;
			$location = add_query_arg( $query_args, $_status['location'] );
			
			$_count_query_args = $count_query_args;			
			$wp_user_query = new WP_User_Query( $_count_query_args );
       		$results = $wp_user_query->get_results();

			$views[] = 	"<a href=\"$location\" class=\"". ( ( is_null( $_status['current'] ) || $_status['current'] === 'any' ) ? 'current' : '' ) ."\">". ( is_string( $_status['show_all'] ) ? $_status['show_all'] : __( 'Tous', 'tify' ) ) ." <span class=\"count\">(". 
						$wp_user_query->get_total().
						")</span></a>";
		endif;	
				
		foreach( $_status['available'] as $status  => $label ) :
			$query_args = array();
			foreach( (array) $_status['query_args'] as $args => $value ) :
				$query_args[$args] = sprintf( $value, $status );
			endforeach;
			$location = add_query_arg( $query_args, $_status['location'] );	
			
			$_count_query_args = $count_query_args;	
			$_count_query_args['meta_query']['relation'] = 'AND'; 
			$_count_query_args['meta_query'][] = array(
				'key' 		=> $wpdb->get_blog_prefix( ) .'tify_membership_status',
				'value'		=> $status,
				'type'		=> 'NUMERIC'
			);
			$wp_user_query = new WP_User_Query( $_count_query_args );
       		$results = $wp_user_query->get_results();

			$views[] = 	"<a href=\"$location\" class=\"". ( ( (string) $status === $_status['current'] ) ? 'current' : '' ) ."\">$label <span class=\"count\">(". 
						$wp_user_query->get_total().
						")</span></a>";	
		endforeach;
		
		return $views;
	}
			
	/* = COLONNES = */
	/** == Définition des colonnes == **/
	public function get_columns() {
		$c = array(
			'cb'       			=> '<input type="checkbox" />',
			'user_login' 		=> __( 'Identifiant', 'tify' ),
			'display_name'		=> __( 'Nom', 'tify' ),
			'user_email'		=> __( 'E-mail', 'tify' ),
			'user_registered'	=> __( 'Date d\'enregistrement', 'tify' )
		);
	
		return $c;
	}
	
	/** == Définition de l'ordonnancement par colonne == **/
	public function get_sortable_columns() {
		$c = array(
			'user_login' 		=> 'user_login',
			'display_name'     	=> 'display_name',
			'user_email'    	=> 'user_email',
			'user_registered'	=> 'user_registered'
		);

		return $c;
	}
	
	/** == Contenu personnalisé : Case à cocher == **/
	public function column_cb( $item ){
        return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->ID );
    }
	
	/** == Contenu personnalisé : Login == **/
	function column_user_login( $item ){
		$edit_link = esc_url( add_query_arg( array( 'user_id' => $item->ID ), $this->main->edit_link ) );
		$actions = array();

		if ( current_user_can( 'edit_user',  $item->ID ) ) :
			$edit = "<strong><a href=\"{$edit_link}\">$item->user_login</a></strong><br />";
			$actions['edit'] = "<a href=\"{$edit_link}\">". __( 'Editer', 'tify' ) . "</a>";
		else :
			$edit = "<strong>$item->user_login</strong><br />";
		endif;
	
		$edit .= $this->row_actions( $actions );
		
		return $edit;
	}
	
	/** == Contenu personnalisé : Rôle == **/
	function column_user_registered( $item ){
		return mysql2date( __( 'd/m/Y à H:i', 'tify' ), $item->user_registered );
	}
		
	/* = TRAITEMENT DES ACTIONS = */
	function process_bulk_action(){
		if( $this->current_action() ) :	
			switch( $this->current_action() ) :
				case 'delete' :									
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );	
					wp_redirect( $sendback );
					exit;
				break;
				case 'trash' :									
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );	
					wp_redirect( $sendback );
					exit;
				break;
				case 'untrash' :									
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );	
					wp_redirect( $sendback );
					exit;
				break;		
			endswitch;
		elseif( ! empty( $_REQUEST['_wp_http_referer'] ) ) :
	 		wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI']) ) );
	 		exit;
		endif;
	}
}

/* = FORMULAIRE D'EDITION = */
class tiFy_Forum_Contributors_EditForm extends tiFy_AdminView_Edit_Form{
	/* = ARGUMENTS = */
	public	// Configuration
			$roles,
			
			// Paramètres
			$item,
			$is_profile_page,
			$current_user,
			
			// Référence
			$main;
	
	/* = CONSTRUCTEUR = */	
	public function __construct( tiFy_Forum_Contributors $main ){
		// Instanciation de la classe de référence
		$this->main 	= $main;
		
		// Configuration
		$this->roles 	= $this->main->master->roles;
		
		// Actions et filtres Wordpress
		add_action( 'load-'. $this->main->hookname, array( $this, 'wp_load' ) );
	}
	
	/* = ACTION ET FILTRE WORDPRESS = */
	function wp_load(){
		$this->main->master->tify_forms->mkcf->forms->set_current( $this->main->master->form_id );
	}
		
	/* = PREPARATION DE L'OBJECT A ÉDITER = */
	function prepare_item(){
		$user_id 	= ( isset( $_REQUEST['user_id'] ) ) ? (int) $_REQUEST['user_id'] : 0;
		$this->item = get_user_to_edit( $user_id );
		$this->current_user = wp_get_current_user();
		if ( ! $this->is_profile_page )
			$this->is_profile_page =  ( $this->item->ID == $this->current_user->ID );
	}
	
	/* = VUES = */
	/** == Attribututs de la balise formulaire == **/
	function form_attrs(){		
		echo 	"id=\"your-profile\"".
				" action=\"\"".
				" method=\"post\"".
				" novalidate=\"novalidate\"".
				do_action( 'user_edit_form_tag' );
	}
	
	/** == Champs cachés == **/
	function hidden_fields(){
		wp_nonce_field( 'update-user_' . $this->item->ID );
	?>
		<p>
			<input type="hidden" name="from" value="profile" />
			<input type="hidden" name="checkuser_id" value="<?php echo get_current_user_id(); ?>" />
		</p>
		<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $this->item->ID ); ?>" />
	<?php
	}
	
	/** == Formulaire de saisie == **/
	function form(){
		global $tify_tabooxes_master;
		$tify_tabooxes_master->boxes[get_current_screen()->id]->box_render();
	}
	
	/* = NOTIFICATIONS =*/
	/** == Affichage == **/
	function notifications(){
		if ( $notice = $this->get_notices() ) : ?>
			<div id="notice" class="notice notice-warning">
				<p><?php echo $notice ?></p>
			</div>
		<?php endif; ?>
		<?php if ( $error = $this->get_errors() ) : ?>
			<div id="error" class="notice notice-error">
				<p><?php echo $error ?></p>
			</div>
		<?php endif; ?>
		<?php if ( $message = $this->get_messages() ) : ?>
			<div id="message" class="updated notice notice-success is-dismissible">
				<p><?php echo $message; ?></p>
			</div>
		<?php endif;
	}
	
	/** == Récupération des notices == **/
	function get_notices(){
		if( ! isset( $_GET['notice'] ) )
			return;
		switch( $_GET['notice'] ) :
			default :
				break;	
		endswitch;
	}
	
	/** == Récupération des erreurs == **/
	function get_errors(){
		if( $this->main->master->tify_forms->mkcf->errors->has() )
			return $this->main->master->tify_forms->mkcf->errors->display();
		elseif( ! isset( $_GET['error'] ) )
			return;
		switch( $_GET['error'] ) :
			default :
				break;		
		endswitch;
	}
	
	/** == Récupération des messages == **/
	function get_messages(){
		if( ! isset( $_GET['message'] ) )
			return;
		switch( $_GET['message'] ) :
			default :
				break;
			case 1 :
				return __( 'L\'utilisateur a été enregistrée avec succès', 'tify' );
				break;		
		endswitch;	
	}	
}