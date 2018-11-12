<?php
/** == Import Export CSV == **/
class tiFy_Csv_Import{
	/* = ARGUMENTS = */
	public 	// Chemins
			$dir,
			$uri,
			
			// Configuration
			$hook_suffix,
			$page_title,
			$table_name,
			$primary_key = 'ID',
			$upload_dir,
			
			// Paramètres du fichier d'example
			$sample_active		= true,
			$sample_filename	= 'import-exemple.csv',
			$sample_lines		= array(),
			
			// Paramètres d'import CSV
			$length 	= 0,
			$delimiter 	= ",",
			$enclosure 	= "\"",
			$escape 	= "\\",
			$offset 	= 1,
			$limit 		= 1,
							
			// Paramètres d'import des données
			$defaults 			= array(),		// Argument par défaut des données d'import
			$columns_attrs 		= array(),		// Attributs de colonnes 
			$columns_map 		= array(),		// Correspondances entre les entêtes de colonnes et les entêtes de colonnes de la table SQL 
			$columns_update 	= array();		// Colonnes des données de mise à jour
			
			
	private	// Données de class
			$row_error 	= array(),
			$items 		= array(),	
			$class,
			$meta_type,	
			
			// Contrôleur
			$list_table;
	
	/* = CONSTRUCTEUR = */
	public function __construct(){
		// Définition des chemins
		$this->dir = dirname( __FILE__ );
		$this->uri = plugin_dir_url( __FILE__ );		
		
		// Configuration
		$this->class = get_class($this);
		if( ! $this->upload_dir ) :
			$upload_dir = wp_upload_dir();
			$this->upload_dir = $upload_dir['basedir'];
		endif;
		if( ! $this->table_name )
			$this->table_name = 'posts';
		
		if( $this->table_name == 'posts' )
			$this->meta_type = 'post';
		elseif( $this->table_name == 'users' )
			$this->meta_type = 'user';
		else
			$this->meta_type = $this->table_name;
		
		$this->_init_cols( );
		
		// Actions et Filtres Wordpress
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_tify_csv_download_sample_'. $this->class, array( $this, 'wp_ajax_download_sample' ) );
		add_action( 'wp_ajax_tify_csv_upload_'. $this->class, array( $this, 'wp_ajax_upload' ) );
		add_action( 'wp_ajax_tify_csv_import_'. $this->class, array( $this, 'wp_ajax_import' ) );
	}
	
	/* == METHODES PUBLIQUES == */	
	/*** === Vérification d'intégrité de la valeur d'une donnée de colonne == **/
	public function is_col_data_error( $col_data, $col = null ){
		return false;		
	}
		
	/*** === Traitement de la valeur d'une donnée de colonne == **/
	public function parse_col_data( $col_data, $col = null ){
		return $col_data;
	}
	
	/*** === Affichage des options d'import == **/
	public function display_import_options(){}
	
	/*** === Traitement des valeurs par défaut d'une ligne == **/
	public function parse_row_defaults( $row_datas ){
		return $row_datas;
	}
	
	/** == @todo Traitement de l'import de données d'une ligne == **/
	public function parse_row_import( $row_datas ){}
	
	/** == Post-traitement de l'import de données d'une ligne == **/
	public function post_row_import( $item_id ){ return $item_id; }
	
	/* = CONFIGURATION = */
	/** ==  Initialisation des colonnes == **/
	private function _init_cols( ){
		foreach( $this->columns_attrs as &$col )
			$col = $this->_parse_col( $col );
	}
	
	/** ==  Traitement de la colonne == **/
	private function _parse_col( $col ){
		$defaults = array(
			'title' 		=> '',
			'data_type' 	=> false  // metadata | tax_input[taxo]
		);
		return wp_parse_args( $col, $defaults );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */	
	/** == Mise en file des scripts == **/
	function wp_admin_enqueue_scripts(){
		tify_progress_enqueue();
		wp_enqueue_style( 'tify_csv', $this->uri .'tify_csv.css', array(), 150607 );
		wp_enqueue_script( 'tify_csv', $this->uri .'tify_csv.js', array( 'jquery' ), 150607 );
	}
	
	/** == Fichier d'exemple a télécharger == **/
	function wp_ajax_download_sample(){
		if( ! $this->sample_active )
			wp_die( __( '<h1>Téléchargement du fichier impossible</h1><p>La fonctionnalité n\'est pas active</p>', 'tify' ), __( 'Impossible de télécharger le fichier', 'tify' ), 404 );
		
		header( 'Content-Type: application/csv' );
	    header( 'Content-Disposition: attachment; filename="'. $this->sample_filename .'";' );
	
	    $f = fopen('php://output', 'w');
		
		if( ! empty( $this->sample_lines ) ) :
			$sample_lines = $this->sample_lines;			
		elseif( !empty( $this->columns_attrs ) ) :
			foreach( $this->columns_attrs as $column )
				$sample_lines[0][] = $column['title'];
		else :
			global $wpdb;
			foreach( $wpdb->get_col( "DESC {$wpdb->{$this->table_name}}", 0 ) as $column )
				$sample_lines[0][] = $column;
			$sample_lines[1] = $wpdb->get_row( "SELECT * FROM {$wpdb->{$this->table_name}}", ARRAY_A );
		endif;		
		
	    foreach ( $sample_lines as $line )
	        fputcsv( $f, $line, $this->delimiter, $this->enclosure, $this->escape );
		exit;
	}
	
	/** == Traitement Ajax de téléchargement du fichier == **/
	function wp_ajax_upload(){
		$results = array();		
		$file = current( $_FILES );	
		$filename = sanitize_file_name( basename( $file['name'] ) );
		
		if( ! @move_uploaded_file( $file['tmp_name'],  $this->upload_dir . "/" . $filename  ) )
			$results['error'] = sprintf( __( 'Impossible de déplacer le fichier "%s" dans le dossier d\'import', 'tify' ), basename( $file['name'] ) );
		elseif( $csv = file( $this->upload_dir . "/" . $filename ) );
			$results = array( 'basename' => $filename, 'total' => count( $csv ), 'offset' => $this->offset, 'limit' => $this->limit );		
							
		if ( ( $handle = fopen( $this->upload_dir . "/" . $filename, "r" ) ) !== FALSE ) :
			$n = 0; $row = 0; $column_headers = array();
		    while ( ( ( $data = fgetcsv( $handle, $this->length, $this->delimiter, $this->enclosure, $this->escape ) ) !== FALSE ) ) :
		       	// Préparation des colonnes
		       	if( ! $row  ) :
					if( $this->offset && ! $this->columns_attrs ) :
						for ( $c=0; $c < count( $data ); $c++ ) 
							$column_headers[$c] = $data[$c];
						if( $column_headers ) :
							$this->columns_attrs = $this->_parse_column_headers( $column_headers );
						endif;
					else :
						$column_headers = array_keys( $this->columns_attrs );
					endif;
					$row++;
					continue;			
				endif;
				
				// Démarrage à partir de l'offset 
		       	if( $this->offset && ( $row < $this->offset ) ) 
					continue;
				
				// Préparation de l'item courant
				$this->items[$row] = new stdClass();
				$this->items[$row]->row = $row; 
				$this->items[$row]->cb =	"<input type=\"checkbox\" />";       	
				
				// Récupération des données du fichier csv						     
		        for ( $c=0; $c < count( $data ); $c++ ) :
					if( ! $column_index = $this->_map_column( $column_headers[$c] ) )
						continue;
					if( $error = $this->is_col_data_error( $data[$c], $column_index ) )
						$this->_set_row_error( $row, $error );
				 
					$this->items[$row]->{$column_index} = $this->parse_col_data( $data[$c], $column_index );
				endfor;
				
				$this->items[$row]->_tify_csv_result = $this->_get_row_results( $row );
	
				$row++;
			endwhile;
		    fclose($handle);
		endif;
		
		$output = "";
		
		$output = "<h3>". __( 'Aperçu des données avant l\'import', 'tify' ) ."</h3>";		
		// Affichage de la table des données
		$this->list_table = new tiFy_Csv_List_Table( $this );
		list( $columns, $hidden ) = $this->list_table->get_column_info();
		$this->list_table->items = $this->items;
		ob_start();
		$this->list_table->display();
		$output .= ob_get_clean();
		
		$results['html'] = $output;
							
		echo json_encode( $results );
		exit;
	}

	/** == Traitement Ajax d'import des données == **/
	function wp_ajax_import(){
		$basename 	= $_POST['basename'];		
		$offset 	= (int) $_POST['offset'];
		$limit 		= (int) $_POST['limit'];
		$results 	= array();		
			
		$output = "";		
		if ( ( $handle = fopen( $this->upload_dir . "/". basename( $basename ), "r" ) ) !== FALSE ) :
			$row = 0; $column_headers = array();
		    while ( ( ( $data = fgetcsv( $handle, $this->length, $this->delimiter, $this->enclosure, $this->escape ) ) !== FALSE ) ) :
		       	// Préparation des colonnes
		       	if( ! $row  ) :
					if( $this->offset && ! $this->columns_attrs ) :
						for ( $c=0; $c < count( $data ); $c++ ) 
							$column_headers[$c] = $data[$c];
						if( $column_headers ) :
							$this->columns_attrs = $this->_parse_column_headers( $column_headers );
						endif;
					else :
						$column_headers = array_keys( $this->columns_attrs );
					endif;
					$row++;
					continue;
				elseif( $row < $offset ) :
					$row++; continue;				
				elseif( $row < ( $offset+$limit ) ) :			
					 $results[$row] = $this->_parse_row_import( $data ); $row++;
				else :
					break;
				endif;
			endwhile;
		    fclose($handle);
		endif;
		
		echo json_encode( $results );
		exit;
	}

	/* = CONTROLEUR = */	
	/** ==  Traitement des entêtes de colonnes == **/
	private function _parse_column_headers( $column_headers ){
		global $wpdb;		
		
		$cols = array();
		
		foreach( $column_headers as $column_name ) :
			$col = array();
			
			$col['title'] = $column_name;
			$column_index = $this->_map_column( $column_name );
			
			$table_columns = $wpdb->get_col( "DESC {$wpdb->{$this->table_name}}", 0 );
			$col['data_type'] = ( in_array( $column_index, $table_columns ) ) ? false : 'metadata';
 			
			$cols[$column_index] = $this->_parse_col( $col );
		endforeach;
		
		return $cols;
	}
	
	/** == Mapping des colonnes du fichier CSV == **/
	private function _map_column( $column_name ){
		$column_name = trim( strtolower( $column_name) );
		
		$column_mapped = sanitize_key( $column_name );
		foreach( $this->columns_map as $mapping => $c ) :
			if( is_string( $c ) )
				$c = array( $c );
			if( in_array( $column_name, array_map( 'strtolower', $c ) ) ) :
				$column_mapped = $mapping;
				break;
			endif;
		endforeach;
		
		return $column_mapped;
	}
	
	/*** === Prétraitement des valeurs par défaut === ***/
	private function _pre_parse_row_defaults( $item ){
		$item = wp_parse_args( $item, $this->defaults );
		
		return $this->parse_row_defaults( $item );
	}	
	
	/*** === Définition d'une erreur === ***/
	private function _set_row_error( $row, $error ){
		$this->row_error[$row][] = $error;
	}
	
	/*** === Récupération des erreurs === ***/
	private function _get_row_results( $row ){
		$output  = "";
		if( isset( $this->row_error[$row] ) ) :
			$output .= "<strong style=\"color:red;text-transform:uppercase\">". __( 'Import impossible', 'tify' ) ."</strong>";
			$output .= "<ol style=\"line-height:1;margin:0;padding:0;margin-left:1em;color:red;\">";
			foreach( $this->row_error[$row] as $error )
				$output .= "<li style=\"line-height:1;margin:0;padding:0;\">{$error}</li>";
			$output .= "</ol>";
		elseif( $this->_is_update_row( $row ) ) :
			$output .= "<em style=\"color:green;\">".__( 'Va être mis à jour', 'tify' ) ."</em>";
		endif;	
		
		return $output;
	}
	
	/*** === Vérification de la mise à jour de la ligne de donnée === ***/
	private function _is_update_row( $row ){
		if( empty( $this->columns_update ) )
			return false;
		
		global $wpdb;
		
		if( ! $this->items[$row] )
			return false;
		
		$query  = "SELECT * FROM {$wpdb->{$this->table_name}} WHERE 1"; 
		foreach( $this->columns_update as $colup )
			$query .= " AND {$wpdb->{$this->table_name}}.{$colup} = '". $this->items[$row]->{$colup} ."'";	
		
		return $wpdb->query( $query );
	}
	
	/** == Traitement de l'import de données == **/
	private function _parse_row_import( $row_datas ){
		global $wpdb;
				
		// Traitement et enregistrement des données de post
		$item = array();
		foreach( array_keys( $this->columns_attrs ) as $k => $col ) :
			if( $error = $this->is_col_data_error( $row_datas[$k], $col ) )
				return false;
			if( ! $this->columns_attrs[$col]['data_type'] )
				$item[$col] = $this->parse_col_data( $row_datas[$k], $col );
		endforeach;
		
		// Bypass
		if( ! $item = $this->_pre_parse_row_defaults( $item ) ) 
			return false;
		
		$update = false; $item_id = false;
		if( ! empty( $this->columns_update ) ) :
			$where = array();
			$query  = "SELECT {$this->primary_key} FROM {$wpdb->{$this->table_name}} WHERE 1"; 
			foreach( $this->columns_update as $colup ) :
				$query .= " AND {$wpdb->{$this->table_name}}.{$colup} = '". $item[$colup] ."'";	
				$where[$colup] = $item[$colup];
			endforeach;	
			if( !empty( $where ) && ( $item_id = $wpdb->get_var( $query ) ) )
				$update = true;	
		endif;
				
		if( $update ) :
			$wpdb->update( $wpdb->{$this->table_name}, $item, $where );
		elseif( $wpdb->insert( $wpdb->{$this->table_name}, $item ) ) :
			$item_id = $wpdb->insert_id;
		endif;
		
		// Traitement et enregistrement des metadonnées de post
		reset( $this->columns_attrs );
		if( $item_id ) :
			foreach( array_keys( $this->columns_attrs ) as $k => $meta_key ) :				
				if( $this->columns_attrs[$meta_key]['data_type'] === 'metadata' ) :
					$prev_value = $update ? get_metadata( $this->meta_type, $item_id, $meta_key, true ) : '';
					update_metadata( $this->meta_type, $item_id, $meta_key, $this->parse_col_data( $row_datas[$k], $meta_key ), $prev_value );
				endif;
			endforeach;
		endif;
		
		// @todo Traitement et enregistrement des taxonomies
		
		// Post-Traitement de l'import		
		$this->post_row_import( $item_id );
		
		return $item_id;	
	}
	
	/* = VUES = */
	/** == Rendu de l'interface d'administration == **/
	public function admin_render(){
	?>
		<div class="wrap">
			<h2>
				<?php echo $this->page_title ? $this->page_title : __( 'Importation d\'éléments', 'tify' );?>
				<?php if( $this->sample_active ) :?>
				<a id="tify_csv-download_sample" class="add-new-h2" href="<?php echo esc_url( add_query_arg( array( 'action' => 'tify_csv_download_sample_'. $this->class ), admin_url( 'admin-ajax.php') ) );?>"><?php _e( 'Fichier d\'exemple', 'tify' );?></a>
				<?php endif;?>
			</h2>
			<h3><?php _e( 'Téléchargement du fichier d\'import', 'tify' );?></h3>
			<div>
				<form id="tify_csv-uploadfile" method="post" action="" enctype="multipart/form-data">
					<input id="tify_csv-class" type="hidden" name="tify_csv-class" value="<?php echo $this->class;?>">
					<input id="tify_csv-uploadfile_button" type="file" name="" autocomplete="off">	<span class="spinner"></span>
				</form>				
				
				<div id="tify_csv-list_table_container"></div>
			</div>
			<?php ob_start(); $this->display_import_options(); $display_import_options = ob_get_clean();?>
			<form id="tify_csv-import" method="post" action="">
				<?php if( $display_import_options ) :?>
				<div id="tify_csv-import-options">
					<h3><?php _e( 'Options d\'import', 'tify' );?></h3>
					<?php echo $display_import_options;?>
					<hr/>
				</div>
				<?php endif;?>	
				<button type="submit" id="tify_csv-import_button" class="button-secondary"><span class="dashicons dashicons-migrate" style="vertical-align:middle"></span> <?php _e( 'Lancer l\'import', 'tify');?></a>
			</form>
		</div>
	<?php
	}
}

if( ! is_admin() )
	return;
tify_require( 'admin_view' );

/* = LISTE = */
class tiFy_Csv_List_Table extends tiFy_AdminView_List_Table {
	/* = ARGUMENTS = */	
	public 	// Contrôleur
			$main;
	
	/* = CONSTRUCTEUR = */	
	public function __construct( tiFy_Csv_Import $main ){
		// Définition du controleur principal	
		$this->main = $main;	
		
		// Définition de la classe parente
       	parent::__construct( 
       		array(
            	'singular'  => 'tify_csv',
            	'plural'    => 'tify_csvs',
            	'screen'	=> $this->main->hook_suffix
        	)        	 
		);
	}
	
	/** == Définition des colonnes == **/
	public function get_columns(){
		$c['row'] = '#';	
		foreach( $this->main->columns_attrs as $col => $args ) :
			$c[$col] = $args['title'] . "<em style=\"display:block;font-size:0.8em;line-height:1;color:#999;\">". ( ! $args['data_type'] ? __( 'Donnée principale', 'tify' ) : __( 'Metadonnée', 'tify' ) );
		endforeach;
		$c['_tify_csv_result'] = '';
		
		return $c;
	}
	
	/** == Contenu personnalisé : Case à cocher == **/
	public function column_cb( $item ){
        return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->row );
    }
}