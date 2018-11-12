<?php
class tiFy_AdminView_Edit_Form{
	/* = ARGUMENTS = */
	public	// Paramètres
			$screen,
			$item,
			$notifications,
			
			// Contrôleurs
			$db;
	
	/* = CONSTRUCTEUR = */	
	public function __construct( $args = array(), $db = null ){
		$args = wp_parse_args( $args, array(
			'ajax' => false,
			'screen' => null,
		) );
		$this->screen = convert_to_screen( $args['screen'] );
		
		// Définition des contrôleurs
		$this->db = $db;
		
		// Actions et filtres Wordpress
		add_action( 'current_screen', array( $this, 'wp_current_screen' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Chargement == **/
	public function wp_current_screen(){
		if( get_current_screen()->id === $this->screen->id )
			$this->process_actions();
	}
	
	/* = CONFIGURATION = */
	/** == Préparation de l'object à éditer == **/
	public function prepare_item(){}
		
	/** == Définition des erreurs == **/
	public function set_errors(){}
	
	/** == Définition des messages == **/
	public function set_messages(){}
	
	/** == Définition des notices == **/
	public function set_notices(){}
	
	
	/* = CONTRÔLEUR = */
	/** == Récupération de l'action courante == **/
	public function current_action() {
		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
			return $_REQUEST['action'];

		if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
			return $_REQUEST['action2'];

		return false;
	}
	/** == Execution des actions == **/
	public function process_actions(){}
	
	/** == Récupération des notices == **/
	function get_notices(){
		// Bypass
		if( ! isset( $_GET['notice'] ) )
			return;
		if( ! $notices = $this->set_notices() )
			return;
		if( ! isset( $notices[ $_GET['notice'] ] ) )
			return;
		
		return $notices[ $_GET['notice'] ];
	}
	
	/** == Récupération des erreurs == **/
	function get_errors(){
		// Bypass		
		if( ! isset( $_GET['error'] ) )
			return;
		if( ! $errors = $this->set_errors() )
			return;
		if( ! isset( $errors[ $_GET['error'] ] ) )
			return;
		
		return $errors[ $_GET['error'] ];
	}
	
	/** == Récupération des messages == **/
	function get_messages(){
		// Bypass
		if( ! isset( $_GET['message'] ) )
			return;
		if( ! $messages = $this->set_messages() )
			return;		
		if( ! isset( $messages[ $_GET['message'] ] ) )
			return;
		
		return $messages[ $_GET['message'] ];	
	}	
	
	/* = VUES = */
	/** == Affichage des messages de notifications == **/
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
	
	/** == Attributs du formulaire == **/
	public function form_attrs(){}
	
	/** == Champs cachés == **/
	public function hidden_fields(){}
	
	/** == Affichage de l'interface de saisie == **/
	public function display(){
	?>
		<form method="post">
			<div style="margin-right:300px; margin-top:20px;">
				<div style="float:left; width: 100%;">					
					<?php $this->hidden_fields();?>
					<?php $this->form();?>					
				</div>
				<div style="margin-right:-300px; width: 280px; float:right;">
					<?php $this->submitdiv();?>
				</div>
			</div>
		</form>
	<?php
	}
	
	/** == Affichage du formulaire d'édition == **/
	public function form(){}
	
	/** == Affichage de la boîte de soumission du formulaire == **/
	public function submitdiv(){
	?>
		<div id="submitdiv" class="tify_submitdiv">
			<h3 class="hndle">
				<span><?php _e( 'Enregistrer', 'tify' );?></span>
			</h3>
			<div class="inside">
				<div class="minor_actions">
					<?php $this->minor_actions();?>
				</div>	
				<div class="major_actions">
					<?php $this->major_actions();?>
				</div>	
			</div>
		</div>			
	<?php
	}
	
	/** == Affichage des actions secondaire de la boîte de soumission du formulaire == **/
	public function minor_actions(){}
	
	/** == Affichage des actions principale de la boîte de soumission du formulaire == **/
	public function major_actions(){
	?>
		<div class="publishing">
			<button class="button-primary"><?php _e( 'Enregistrer', 'tify');?></button>
		</div>
	<?php
	}	
}

/* = LISTE = */
/** 
 * @see https://codex.wordpress.org/Class_Reference/WP_List_Table
 */
if( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH .'wp-admin/includes/class-wp-list-table.php' );
	
class tiFy_AdminView_List_Table extends WP_List_Table{
	/* = ARGUMENTS = */
	public	// Configuration
			$per_page_option = 'tify_adminview_per_page_option',
			$per_page_default = 20,
			// Callback
			$cb_count_items_query,
			$cb_get_items_query,
			// Contrôleurs
			$db;
			
	/* = CONSTRUCTEUR = */	
	public function __construct( $args = array(), $db = null ){
		// Définition des arguments
       	parent::__construct( 
       		wp_parse_args(
       			$args,
	       		array(
		            'singular'  => '',
		            'plural'    => '',
		            'ajax'      => true,
		            'screen'	=> ''
	        	)
			)
		);
			
		// Définition des contrôleurs
		$this->db = $db;
		
		// Définition de la configuration
		$this->cb_count_items_query = $this->db ? array( $this->db, 'count_items' ) : '__return_zero';
		$this->cb_get_items_query 	= $this->db ? array( $this->db, 'get_items' ) : '__return_empty';
		
		// Actions et filtres Wordpress
		add_action( 'current_screen', array( $this, 'wp_current_screen' ), 99 );
	}
	
	/* = CONFIGURATION = */
	/** == Définition des statuts == **/
	public function set_status(){
		return array();
	}
		
	/** == Traitement des requêtes de récupération standard == **/
	public function parse_query_items(){
		// Récupération des arguments
		$status	= isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'any';
		$search = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
		$per_page = $this->get_items_per_page( $this->per_page_option, $this->per_page_default );
		$paged = $this->get_pagenum();
		
		// Arguments par défaut
		$args = array(						
			'per_page' 	=> $per_page,
			'paged'		=> $paged,
			'search' 	=> $search,
			'order' 	=> 'DESC',
			'orderby' 	=> $this->db->primary_col
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
	
	/** == Traitement des requêtes de récupération particulières == **/
	public function extra_parse_query_items(){
		return array();
	}
	
	/** == Définition des erreurs == **/
	public function set_errors(){}
	
	/** == Définition des messages == **/
	public function set_messages(){}
	
	/** == Définition des notices == **/
	public function set_notices(){}
		
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Chargement de la page == **/
	function wp_current_screen(){
		if( get_current_screen()->id !== $this->screen->id )
			return;
		$this->process_bulk_action();
	}
	
	/* = PREPARATION DES ITEMS = */
	public function prepare_items() {				
		// Récupération des items
		$query_items_args = $this->parse_query_items(); 
		$this->items = call_user_func( $this->cb_get_items_query, $query_items_args );		
		
		// Pagination
		$total_items = call_user_func( $this->cb_count_items_query, $query_items_args );
		$per_page = $this->get_items_per_page( $this->per_page_option, $this->per_page_default );
		$this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                    
            'total_pages' => ceil( $total_items / $per_page )
			) 
		);
	}
	
	/* = VUES = */
	/** == Affichage des messages de notifications == **/
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
	
	/* = ORGANES DE NAVIGATION = */
	/** == Filtrage principal  == **/
	public function get_views(){
		// Bypass
		if( ! $_status = $this->_get_status() )
			return;
		
		$views = array();
		if( $_status['show_all'] ) :
			$query_args = array();
			foreach( (array) $_status['query_args'] as $args => $value ) :
				$query_args[$args] = sprintf( $value, 'any' );
			endforeach;
			$location = add_query_arg( $query_args, $_status['location'] );
			
			$count_query_args = array();
			foreach( (array) $_status['count_query_args'] as $args => $value ) :
				$count_query_args[$args] = sprintf( $value, 'any' );
			endforeach;	

			$views[] = "<a href=\"$location\" class=\"". ( ( empty( $_status['current'] ) || $_status['current'] === 'any' ) ? 'current' : '' ) ."\">". ( is_string( $_status['show_all'] ) ? $_status['show_all'] : __( 'Tous', 'tify' ) ) ." <span class=\"count\">(". call_user_func( $this->cb_count_items_query, $count_query_args ) .")</span></a>";
		endif;	
				
		foreach( $_status['available'] as $status  => $label ) :
			$query_args = array();
			foreach( (array) $_status['query_args'] as $args => $value ) :
				$query_args[$args] = sprintf( $value, $status );
			endforeach;
			$location = add_query_arg( $query_args, $_status['location'] );	
			
			$count_query_args = array();
			foreach( (array) $_status['count_query_args'] as $args => $value ) :
				$count_query_args[$args] = sprintf( $value, $status );
			endforeach;	
						
			$views[] = "<a href=\"$location\" class=\"". ( $status === $_status['current'] ? 'current' : '' ) ."\">$label <span class=\"count\">(". call_user_func( $this->cb_count_items_query, $count_query_args ) .")</span></a>";	
		endforeach;
		
		return $views;
	}
	
	/** == Filtrage secondaire == **/
	protected function extra_tablenav( $which ) {}
			
	/* = COLONNES = */
	/** == Définition des colonnes == **/
	public function get_columns() {
		$c = array(
			'cb' => '<input type="checkbox" />'
		);	
		return $c;
	}
		
	/** == Définition de l'ordonnancement par colonne == **/
	public function get_sortable_columns() {
		$c = array(	);

		return $c;
	}
		
	/** == Contenu par défaut des colonnes == **/
	public function column_default( $item, $column_name ){
        switch( $column_name ) :
            default:
				if( isset( $item->{$column_name} ) )
					return $item->{$column_name};
			break;
		endswitch;
    }
	
	/** == Contenu personnalisé : Case à cocher == **/
	public function column_cb( $item ){
        return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->{$this->db->primary_key} );
    }
	
	/* == CONTRÔLEURS == */
	/** == Execution des actions == **/
	public function process_bulk_action(){
		if( $this->current_action() ) :
			switch( $this->current_action() ) :
				default :
				break;	
			endswitch; 
		elseif ( ! empty( $_REQUEST['_wp_http_referer'] ) ) :
			wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI']) ) );
	 		exit;
		endif;		
	}
	
	/** == Récupération des notices == **/
	function get_notices(){
		// Bypass
		if( ! isset( $_GET['notice'] ) )
			return;
		if( ! $notices = $this->set_notices() )
			return;
		if( ! isset( $notices[ $_GET['notice'] ] ) )
			return;
		
		return $notices[ $_GET['notice'] ];
	}
	
	/** == Récupération des erreurs == **/
	function get_errors(){
		// Bypass		
		if( ! isset( $_GET['error'] ) )
			return;
		if( ! $errors = $this->set_errors() )
			return;
		if( ! isset( $errors[ $_GET['error'] ] ) )
			return;
		
		return $errors[ $_GET['error'] ];
	}
	
	/** == Récupération des messages == **/
	function get_messages(){
		// Bypass
		if( ! isset( $_GET['message'] ) )
			return;
		if( ! $messages = $this->set_messages() )
			return;		
		if( ! isset( $messages[ $_GET['message'] ] ) )
			return;
		
		return $messages[ $_GET['message'] ];	
	}
	
	/** == Récupération des status == **/
	protected function _get_status( ){
		$defaults = array(
			'available'			=> array( ),
			'show_all'			=> true,
			'current'			=> isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'any',
			'query_args'		=> array( 'status' => '%s' ),
			'count_query_args'	=> array( 'status' => '%s' ),
			'location'			=> esc_attr( wp_unslash( remove_query_arg( array( 'paged', 's' ), $_SERVER['REQUEST_URI'] ) ) )
		);
		return wp_parse_args( $this->set_status(), $defaults );	
	}
}