<?php
class tiFy_plugins{
	var $tiFy,
		$plugin_data,
		$capability,
		$plugins_list_table,
		$plugins = array(),
		$addons = array(),
		$active_plugins = array(),
		$active_addons = array();
		
	/**
	 * Initialisation
	 */
	function __construct(tiFY $master ){		
		$this->tiFy = $master;
		
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}
	
	/**
	 * 
	 */
	function admin_init(){
		$this->plugins_list_table = new tiFy_plugins_list_table( $this->tiFy );		
	}
	
	/**
	 * Menu d'administration
	 */
	function admin_menu(){
		add_submenu_page( 'tify', __( 'Extensions & modules', 'tify' ) , __( 'Extensions/Modules', 'tify' ), $this->tiFy->capability, 'tify', array( $this, 'admin_render' ) );		
	}
	
	/**
	 * Page de l'administration
	 */
	function admin_render(){
		$this->plugins_list_table->prepare_items();    
	?>
	    <div class="wrap">
	        <h2><?php _e( 'Extensions et Modules PresstiFy', 'tiFy' );?></h2>
	
	        <form method="post">
	            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $this->plugins_list_table->display() ?>
	        </form>     
	    </div>
	<?php	
	}
		
	/**
	 * Récupération des plugins
	 */
	function get_plugins(){
	  	if( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	  	
		$plugin_folder = '/'. basename( $this->tiFy->dir ). '/plugins';

		if ( ! empty( $this->plugins ) )
			return $this->plugins;
	
		$wp_plugins = array ();
		$plugin_root = WPMU_PLUGIN_DIR;
		if ( ! empty( $plugin_folder ) )
			$plugin_root .= $plugin_folder;

		// Files in wp-content/plugins directory
		$plugins_dir = @ opendir( $plugin_root);
		$plugin_files = array();

		if ( $plugins_dir ) {
			while (($file = readdir( $plugins_dir ) ) !== false ) {
				if ( substr($file, 0, 1) == '.' )
					continue;
				if ( is_dir( $plugin_root.'/'.$file ) ) {
					$plugins_subdir = @ opendir( $plugin_root.'/'.$file );
					if ( $plugins_subdir ) {
						while (($subfile = readdir( $plugins_subdir ) ) !== false ) {
							if ( substr($subfile, 0, 1) == '.' )
								continue;
							if ( substr($subfile, -4) == '.php' )
								$plugin_files[] = "$file/$subfile";
						}
						closedir( $plugins_subdir );
					}
				} else {
					if ( substr($file, -4) == '.php' )
						$plugin_files[] = $file;
				}
			}
			closedir( $plugins_dir );
		}

		if ( empty($plugin_files) )
			return $wp_plugins;
	
		foreach ( $plugin_files as $plugin_file ) {
			if ( ! is_readable( "$plugin_root/$plugin_file" ) )
				continue;
	
			$plugin_data = get_plugin_data( "$plugin_root/$plugin_file", false, false ); //Do not apply markup/translate as it'll be cached.
	
			if ( empty ( $plugin_data['Name'] ) )
				continue;
	
			$wp_plugins[plugin_basename( $plugin_file )] = $plugin_data;
			$this->get_addons( plugin_basename( $plugin_file ) );
		}

		uasort( $wp_plugins, '_sort_uname_callback' );
	
		$this->plugins = $wp_plugins;
	
		return $wp_plugins;
	}
	
	/**
	 * Vérification de l'état d'activation d'un plugin
	 */
	function is_active_plugin( $plugin_path ){
		if( ! $this->active_plugins )
			$this->active_plugins = get_option( 'tify_active_plugins', array() );

		return in_array( $plugin_path, $this->active_plugins );
	}
	
	/**
	 * Initialisation des plugins actifs
	 */
	function load_active_plugins_with_addons(){
		global $tiFy;

		$tiFy = $this->tiFy;
		// Bypass
	 	if( ! $plugins = $this->get_plugins() )
			return;
		foreach( $plugins as $plugin_path => $plugin_data ) :
			if( ! $this->is_active_plugin( $plugin_path ) ) :
				continue;
			elseif( file_exists( $this->tiFy->dir .'/plugins/'. $plugin_path ) ) :
				require_once( $this->tiFy->dir .'/plugins/'. $plugin_path );
				$this->load_plugin_active_addons( $plugin_path );
			endif;
		endforeach;
	}
	
	/**
	 * Activation d'un plugin
	 */
	function activate_plugin( $plugin_path ){
		$current = get_option( 'tify_active_plugins', array() );
		
		if( ! array_search( $plugin_path, $current ) )
			$current[] = $plugin_path;
		sort( $current );
		
		return update_option( 'tify_active_plugins', $current );		
	}
	
	/**
	 * Désactivation d'un plugin
	 */
	function deactivate_plugin( $plugin_path ){
		$current = get_option( 'tify_active_plugins', array() );
		
		$key = array_search( $plugin_path, $current );
		if( $key !== false )
			unset( $current[$key] );
		sort( $current );
		
		return update_option( 'tify_active_plugins', $current );		
	}
		
	/**
	 * Récupération de la liste des module d'un répertoire
	 */
	function get_addons( $plugin_path = '' ) {
		$plugin_folder = dirname( $plugin_path );
		
		if ( ! empty( $this->addons[ $plugin_path ] ) )
			return $this->addons[ $plugin_path ];
		
		$addons = array();
		$plugin_root = $this->tiFy->dir .'/plugins/'. $plugin_folder;
		if ( ! empty( $plugin_path ) )
			$plugin_root .= '/addons';
		
		$addons_dir = @ opendir( $plugin_root);

		$addon_files = array();
		if ( $addons_dir ) {
			while (($file = readdir( $addons_dir ) ) !== false ) {
				if ( substr($file, 0, 1) == '.' )
					continue;				
				if ( is_dir( $plugin_root.'/'.$file ) ) {
					$addons_subdir = @ opendir( $plugin_root.'/'.$file );
					if ( $addons_subdir ) {
						while (($subfile = readdir( $addons_subdir ) ) !== false ) {
							if ( substr($subfile, 0, 1) == '.' )
								continue;
							if ( substr($subfile, -4) == '.php' )
								$addon_files[] = "$file/$subfile";
						}
						closedir( $addons_subdir );
					}
				} else {
					if ( substr($file, -4) == '.php' )
						$addon_files[] = $file;
				}
			}
			closedir( $addons_dir );
		}

		if ( empty($addon_files) )
			return $addons;
	
		foreach ( $addon_files as $addon_file ) {
			if ( !is_readable( "$plugin_root/$addon_file" ) )
				continue;
	
			$addon_data = $this->get_addon_data( "$plugin_root/$addon_file" );
	
			if ( empty ( $addon_data['Name'] ) )
				continue;
	
			$addons['addons/'.$addon_file] = $addon_data;
		}

		uasort( $addons, '_sort_uname_callback' );
		$this->addons[$plugin_path] = $addons;
	
		return $addons;
	}

	/**
	 * Récupération des informations d'un module
	 */
	function get_addon_data( $addon_file, $markup = true, $translate = true ) {
		$default_headers = array(
			'Name' => 'Addon Name',
			'PluginURI' => 'Addon URI',
			'Version' => 'Version',
			'Description' => 'Description',
			'Author' => 'Author',
			'AuthorURI' => 'Author URI',
			'TextDomain' => 'Text Domain',
			'DomainPath' => 'Domain Path'
		);
	
		$addon_data = get_file_data( $addon_file, $default_headers );
	
		$addon_data['Title']      = $addon_data['Name'];
		$addon_data['AuthorName'] = $addon_data['Author'];
	
		return $addon_data;
	}
	
	/**
	 * Chargement des modules actifs pour une extension 
	 */
	function load_plugin_active_addons( $plugin_path ){		
		// Bypass
	 	if( empty( $this->addons[$plugin_path] ) )
			return;

		foreach( $this->addons[$plugin_path] as $addon_path => $addon_data ) :
			if( ! $this->is_active_addon( dirname( $plugin_path ) ."/". $addon_path ) ) :
				continue;
			elseif( file_exists( $this->tiFy->dir .'/plugins/'. dirname( $plugin_path ) ."/". $addon_path  ) ) :
				require_once( $this->tiFy->dir .'/plugins/'. dirname( $plugin_path ) ."/". $addon_path );
			endif;
		endforeach;
	}	
	
	/**
	 * Vérification de l'état d'activation d'un module
	 * 
	 * @param $addon_full_path : dirname( $plugin_path ) ."/". $addon_path
	 */
	function is_active_addon( $addon_full_path ){
		if( ! $this->active_addons )
			$this->active_addons = get_option( 'tify_active_addons', array() );
		
		return in_array( $addon_full_path, $this->active_addons );
	}
	
	/**
	 * Activation d'un module
	 * 
	 * @param $addon_full_path : dirname( $plugin_path ) ."/". $addon_path
	 */
	function activate_addon( $addon_full_path ){
		$current = get_option( 'tify_active_addons', array() );
		
		if( ! array_search( $addon_full_path, $current ) )
			$current[] = $addon_full_path;
		sort( $current );
		
		return update_option( 'tify_active_addons', $current );		
	}
	
	/**
	 * Désactivation d'un module
	 * 
	 * @param $addon_full_path : dirname( $plugin_path ) ."/". $addon_path
	 */
	function deactivate_addon( $addon_full_path ){
		$current = get_option( 'tify_active_addons', array() );

		$key = array_search( $addon_full_path, $current );
		if( $key !== false )
			unset( $current[$key] );
		sort($current);
		
		return update_option( 'tify_active_addons', $current );		
	}
}

if( ! is_admin () ) 
	return;

if( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/**
 * Classe de la table des enregistrements
 */
class tiFy_plugins_list_table extends WP_List_Table {
	var $master,
		$args;
		
	function __construct(tiFY $master ){	
		// Définition des arguments par défault de la classe parente
       	parent::__construct( array(
            'singular'  => 'plugin',
            'plural'    => 'plugins',
            'ajax'      => true,
            'screen'	=> 'toplevel_page_tify'
        ) );	
		// Héritage de la classe maîtresse
		$this->master = $master;
		
		// Définition des arguments complémentaires
		$this->args['paged'] 		= $this->get_pagenum();		 				
		$this->args['per_page'] 	= 20;
		
		$this->process_bulk_action();
		
		// Actions WP
		add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
		add_action( 'wp_ajax_tify_toggle_addon', array( $this, 'ajax_toggle_addon' ) );
	}

	/**
	 * 
	 */
	function admin_print_footer_scripts( ){
		if( $this->screen->id != get_current_screen()->id )
			return;
	?>
		<script type="text/javascript">
		jQuery( document ).ready( function($){
			$( '.tify_addon-toggle' ).change( function(e){
				$checkbox 	= $(this);
				$closest 	= $(this).closest('li');
				data 		= { action : 'tify_toggle_addon', addon : $checkbox.val(), activate : $checkbox.is( ':checked' ) }		
				
				$.ajax({
					url 		: ajaxurl, 
					data		: data,
					type		: 'POST',
					beforeSend	: function(){
						$closest.css( 'opacity', 0.3 );
					},
					success		: function( resp ){						
						
					},
					complete 	: function (){
						$closest.closest('li').css( 'opacity', 1 );
					},
					async : false
				});
			});				
		});
		</script>
	<?php
	}
	
	/**
	 * 
	 */
	function ajax_toggle_addon(){
		list( $plugin_path, $addon_path ) = preg_split( '/\|/', $_REQUEST['addon'], 2 );
		if( $_REQUEST['activate'] === 'true' )
			$return = $this->master->plugins->activate_addon( dirname( $plugin_path ) .'/'. $addon_path );
		else
			$return = $this->master->plugins->deactivate_addon( dirname( $plugin_path ) .'/'. $addon_path );
			
		echo json_encode( array( $return ) );	
		exit;
	}
			
	/**
	 * Liste des colonnes de la table
	 */
	function get_columns() {
		$columns['cb']	= '<input type="checkbox" />';	
				
		$columns['plugin_title'] = __( 'Extension', 'tify' );
		$columns['description'] = __( 'Description', 'tify' );
	
		return $columns;
	}

	/**
	 * Liste des colonnes de la table pour lequelles le trie est actif
	 */
 	function get_sortable_columns() {
        $sortable_columns = array(
            //'date' => array( 'date', false)
        );
				
        return $sortable_columns;
    }
	
	/**
	 * Affichage d'une ligne de données
	 */
	function single_row( $item ) {
		static $alternate = '';
		$alternate = ( $alternate == '' ? ' alternate' : '' ); 
		$row_class = ( $this->master->plugins->is_active_plugin( $item->ID ) ? " class=\"active$alternate\"" : " class=\"inactive$alternate\"" );

		echo '<tr' . $row_class . '>';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
	
	/**
	 * Affichage du contenu par defaut des colonnes
	 */
	function column_default( $item, $column_name ){
		return $item->$column_name;
    }
	
	/**
	 * Contenu de la colonne "case à cocher"
	 */
	function column_cb( $item ){
        return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], esc_attr( $item->ID ) );
    }
	
	/**
	 * Contenu de la colonne
	 */
	function column_plugin_title( $item ){
		if( ! $this->master->plugins->is_active_plugin( $item->ID ) )
			$actions['activate'] = 	"<a href=\"" 
									. wp_nonce_url( 
										add_query_arg( 
											array( 
												'page' => $_REQUEST['page'], 
												'action' => 'activate', 
												'plugin' => $item->ID
											),
											admin_url('admin.php') ),
											'tify_activate_plugin_'. esc_attr( $item->ID ) ) 
									."\">". __( 'Activer', 'tify' ) . "</a>";
		else
			$actions['deactivate'] = 	"<a href=\"" 
									. wp_nonce_url( 
										add_query_arg( 
											array( 
												'page' => $_REQUEST['page'], 
												'action' => 'deactivate', 
												'plugin' => $item->ID
											),
											admin_url('admin.php') ),
											'tify_deactivate_plugin_'. esc_attr( $item->ID ) ) 
									."\">". __( 'Désactiver', 'tify' ) . "</a>";
			
     	return ( $this->master->plugins->is_active_plugin( $item->ID ) ) 
     		? sprintf( '<strong>%1$s</strong>%2$s', $item->Name, $this->row_actions( $actions, true ) ) 
			: sprintf( '%1$s%2$s', $item->Name, $this->row_actions( $actions, true ) );
    }	

	/**
	 * Contenu de la colonne "date"
	 */
	function column_description( $item ){
		$output  = "<div class=\"plugin-description\">{$item->Description}</div>";
		$output .= "<div class=\"second plugin-version-author-uri\">";
		$plugin_meta = array();
		if ( !empty( $item->Version ) )
			$plugin_meta[] = sprintf( __( 'Version %s', 'tify' ), $item->Version );
		if ( !empty( $item->Author ) ) :
			$author = $item->Author;
			if ( !empty( $item->AuthorURI ) )
				$author = '<a href="' . $item->AuthorURI . '">' . $item->Author . '</a>';
			$plugin_meta[] = sprintf( __( 'Par %s', 'tify' ), $author );
			endif;
		if ( ! empty( $item->PluginURI ) ) :
			$plugin_meta[] = sprintf( '<a href="%s">%s</a>',
				esc_url( $item->PluginURI ),
				__( 'Allez sur la page de l\'extension', 'tify' )
			);
		endif;
		
		$output .= implode( ' | ', $plugin_meta );
		$output .= "</div>";

		if( ! empty( $this->master->plugins->addons[ $item->ID ] ) ) :
			$output .= "<h4 style=\"margin:0 0 5px;\">". __( 'Activation/Désactivation des modules de l\'extension', 'tify' ) ."</h4>";
		   	$output .= "<ul class=\"tierce plugin-addons\" style=\"margin:0;\">";
			foreach( $this->master->plugins->addons[ $item->ID ] as $addon_path => $addon_data ) :
				$output .= 	"<li>"
							."<label for=\"{$item->ID}". esc_attr( $addon_path ) ."\" title=\"\">"
							."<input type=\"checkbox\" class=\"tify_addon-toggle\" id=\"{$item->ID}". esc_attr( $addon_path ) ."\" "
								." value=\"{$item->ID}|$addon_path\""
								. checked( $this->master->plugins->is_active_addon( dirname( $item->ID ) ."/". $addon_path ), true, false ) ." "
								. ( ! $this->master->plugins->is_active_plugin( $item->ID ) ? "disabled=\"disabled\"" : "" )
								." autocomplete=\"off\" />"
							."<strong>". $addon_data['Name'] ."</strong> | <em>". $addon_data['Description'] ."</em> | v.". $addon_data['Version'] 
							."</label>"
							."</li>";
			endforeach;
			$output .= "</ul>";
		endif;
		
	   return $output;
    }
	
	/**
	 * Action groupées
	 */
    function get_bulk_actions() {
        $actions = array(
            'activate-selected'    	=> __( 'Activer', 'tify' ),
            'deactivate-selected'   => __( 'Désactiver', 'tify' ),
        );
        return $actions;
    }
    
	/**
	 * Execution des actions groupées
	 */
	function process_bulk_action() {
		if( ! isset( $_REQUEST['page'] ) || ( $_REQUEST['page'] != 'tify' ) )
			return;
		
        switch( $this->current_action() ) :
			case 'activate' :
				if ( ! current_user_can( $this->master->capability ) )
					wp_die( __( '<h1>Action non permise</h1><p>Vous n\'avez pas les habilitations suffisantes pour effectuer cette action.</p>', 'tify' ) );
				
				if( ! $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '' )
					return;
				
				check_admin_referer('tify_activate_plugin_' . $plugin);
				
				$this->master->plugins->activate_plugin( $plugin );
				wp_redirect( self_admin_url("admin.php?activate=true&page={$_REQUEST['page']}&paged={$this->args['paged']}" ) );
				exit;
				break;
			case 'deactivate' :
				if ( ! current_user_can( $this->master->capability ) )
					wp_die( __( '<h1>Action non permise</h1><p>Vous n\'avez pas les habilitations suffisantes pour effectuer cette action.</p>', 'tify' ) );
				
				if( ! $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '' )
					return;
				
				check_admin_referer('tify_deactivate_plugin_' . $plugin);
				
				$this->master->plugins->deactivate_plugin( $plugin );
				wp_redirect( self_admin_url("admin.php?deactivate=true&page={$_REQUEST['page']}&paged={$this->args['paged']}" ) );
				break;
			case 'activate-selected' :
				if ( ! current_user_can( $this->master->plugins->capability ) )
					wp_die( __( '<h1>Action non permise</h1><p>Vous n\'avez pas les habilitations suffisantes pour effectuer cette action.</p>', 'tify' ) );
				
				if( ! $plugins = isset( $_POST['plugin'] ) ? (array) $_POST['plugin'] : array() )
					return;
			
				check_admin_referer('bulk-plugins');
				
				foreach( $plugins as $plugin )
					$this->master->plugins->activate_plugin( $plugin );
					
				wp_redirect( self_admin_url("admin.php?activate-multi=true&page={$_REQUEST['page']}&paged={$this->args['paged']}" ) );	
				break;
			case 'deactivate-selected' :
				if ( ! current_user_can( $this->master->plugins->capability ) )
					wp_die( __( '<h1>Action non permise</h1><p>Vous n\'avez pas les habilitations suffisantes pour effectuer cette action.</p>', 'tify' ) );
				
				if( ! $plugins = isset( $_POST['plugin'] ) ? (array) $_POST['plugin'] : array() )
					return;
				
				check_admin_referer('bulk-plugins');
			
				foreach( $plugins as $plugin )
					$this->master->plugins->deactivate_plugin( $plugin );
					
				wp_redirect( self_admin_url("admin.php?deactivate-multi=true&page={$_REQUEST['page']}&paged={$this->args['paged']}" ) );
				break;		
		endswitch;            
    }
	
	/**
	 * Récupération des items
	 */
	function prepare_items() {
		$columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
		
		$this->_column_headers = array( $columns, $hidden, $sortable );	
				
		$this->items = array();		
		if( $plugins = $this->master->plugins->get_plugins() ) :
			foreach( $plugins as $plugin_path => $plugin_data )	:
				$plugin_data['ID'] = $plugin_path;							
				$this->items[] = (object) $plugin_data;
			endforeach;
		endif;	

		$total_items = count( $this->items );
			
		$this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $this->args['per_page'],                     
            'total_pages' => ceil( $total_items/$this->args['per_page'] ) 
        ) );		
	}
}