<?php
class tiFy_db{
	/* = ARGUMENTS = */			
	public	// Configuration
			$install			= true,
			$update				= false,
			$col_prefix_auto 	= true;
	
	/* = CONSTRUCTEUR = */
	function __construct(){
		$this->parse_cols();
		$this->primary_col = 'id';
		$this->primary_key	= $this->col_prefix . $this->primary_col;
		$this->col_names = array(); $this->search_cols = array();	
		foreach( $this->cols as $name => $args ) : 
			// Definition des noms de colonnes
			$this->col_names[] 	= $this->col_prefix . $name;
			$this->col_{$this->col_prefix . $name} = $args;
			// Définition des colonnes de recherche
			if( $args['search'] )
				$this->search_cols[] = $this->col_prefix . $name;
		endforeach;

		$this->wpdb_table = $this->set_table();
		if( $this->has_meta )
			$this->wpdb_metatable = $this->set_table( $this->table .'meta' );
		
		// Actions et filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );		 
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == GLOBAL == **/
	/*** === Initialisation globale de Wordpress === ***/
	function wp_init(){
		// Installation des tables
		$this->install();		
	}
	
	/* = INITIALISATION = */
	/** == Définition des tables == **/
	function set_table( $table = null ){
		global $wpdb;
		
		if( ! $table )
			$table = $this->table;
		
		// Bypass
		if( in_array( $table, $wpdb->tables ) )
			return $wpdb->{$table};
		
		array_push( $wpdb->tables, $table );
				
		$wpdb->set_prefix( $wpdb->base_prefix );
		
		return $wpdb->{$table};
	}
	
	/** == Installation == **/
	function install(){
		global $wpdb;
		
		if( ! $this->install )
			return;
		 
		// Bypass
		if( $current_version = get_option( 'tify_db_'. $this->wpdb_table, 0 ) ) 
			return;
		
		// Création des tables
		require_once( ABSPATH .'wp-admin/install-helper.php' );	
		
		if( version_compare( $current_version, 1, '>=' ) )
			return;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		// Création de la table principale.
		$create_ddl = "CREATE TABLE $this->wpdb_table ( ";
		$_create_ddl = array();
		foreach( $this->col_names as $col_name )
			$_create_ddl[] = $this->create_dll( $col_name );
		$create_ddl .= implode( ', ', $_create_ddl );
		$create_ddl .= ", PRIMARY KEY ( $this->primary_key )";
		$create_ddl .= $this->create_dll_keys();		
		$create_ddl .= " ) $charset_collate;";
		
		maybe_create_table( 
			$this->wpdb_table,
			$create_ddl 
		);
		
		// Création de la table des metadonnées
		if( $this->has_meta ) :
			$meta_create_ddl  = "CREATE TABLE $this->wpdb_metatable ( ";
			$meta_create_ddl .= "meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
  			$meta_create_ddl .= "{$this->table}_id bigint(20) unsigned NOT NULL DEFAULT '0', ";
  			$meta_create_ddl .= "meta_key varchar(255) DEFAULT NULL, ";
			$meta_create_ddl .= "meta_value longtext";
			$meta_create_ddl .= ", PRIMARY KEY ( meta_id )";
			$meta_create_ddl .= ", KEY {$this->table}_id ( {$this->table}_id )";
			$meta_create_ddl .= ", KEY meta_key ( meta_key )";
			$meta_create_ddl .= " ) $charset_collate;";
			
			maybe_create_table( 
				$this->wpdb_metatable,
				$meta_create_ddl 
			);			
		endif;
		
		update_option( 'tify_db_'. $this->wpdb_table, 1 );
	}
	
	/** == == **/
	function create_dll( $col_name ){
		$types_allowed = array( 
			// Numériques
			'tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'decimal', 'float', 'double', 'real', 'bit', 'boolean', 'serial',
			// Dates
			'date', 'datetime', 'timestamp', 'time', 'year',
			//Textes
			'char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext', 'binary', 'varbinary', 'tinyblob', 'mediumblob', 'blob', 'longblob', 'enum', 'set'
			// 
		);
		$defaults = array(
			'type'				=> false,
			'size'				=> false,
			'unsigned'			=> false,			
			'auto_increment'	=> false,
			'default'			=> false
		);		
		$args = wp_parse_args( $this->col_{$col_name}, $defaults );
		extract( $args );
		
		$type = strtolower( $type );
		if( ! in_array( $type, $types_allowed ) )
			return;
		
		$create_ddl  = "";
		$create_ddl .= "$col_name $type";
		
		if( $size )
			$create_ddl .= "($size)";
			
		if( $unsigned || ( $col_name === $this->primary_key ) )	
			$create_ddl .= " UNSIGNED";	
		
		if( $auto_increment || ( $col_name === $this->primary_key ) )	
			$create_ddl .= " AUTO_INCREMENT";
		
		if( ! is_null( $default ) ) :
			if( is_numeric( $default ) )
				$create_ddl .= " DEFAULT ". $default ." NOT NULL";
			elseif( is_string( $default ) )
				$create_ddl .= " DEFAULT '". $default ."' NOT NULL";
			else		
				$create_ddl .=  " NOT NULL";
		else :
			$create_ddl .=  " DEFAULT NULL";
		endif;	
			
		return $create_ddl;
	}
	
	/** == Création des clefs d'index == **/
	function create_dll_keys( ){
		$create_dll_keys = array();
		foreach( $this->col_names as $col_name ) :
			if( empty( $this->col_{$col_name}['key'] ) )
				continue;
			$_create_dll_keys = ( is_array( $this->col_{$col_name}['key'] ) ) ? implode( ', ', $this->col_{$col_name}['key'] ) : $this->col_{$col_name}['key'];
						
			$create_dll_keys[] = "KEY $col_name ( $_create_dll_keys )";
		endforeach;	
		
		if( ! empty( $create_dll_keys ) )
			return ", ". implode( ', ', $create_dll_keys );
	}
	
	/** == Traitement des arguments des colonne == **/
	function parse_cols(){
		foreach( $this->cols as &$col )
			$col = $this->parse_col( $col );
	}
	/** == Assignation des arguments par défaut d'une colonne == **/
	function parse_col( $col ){
		$defaults = array(
			'search' => false
		);
		$col = wp_parse_args( $col, $defaults );
		
		return $col;
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
			'limit' 	=> -1
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );	
		// Requête
		$query  = "SELECT COUNT( {$this->wpdb_table}.{$this->primary_key} ) FROM {$this->wpdb_table}";	
		/// Conditions
		$query .= " WHERE 1";
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
			'orderby' 	=> $this->primary_col
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		$col = ! $col ?  $this->primary_col : $col;
		if( $this->col_prefix_auto )
			$col = $this->col_prefix . $col;
		// Requête
		$query  = "SELECT {$this->wpdb_table}.{$col} FROM {$this->wpdb_table}";			
		/// Conditions
		$query .= " WHERE 1";
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
		
	/** == Récupération des ids d'élément == **/
	function get_items_ids( $args = array() ){
		return $this->get_items_col( null, $args );	
	}
	
	/** == Récupération d'un élément selon son id == **/
	function get_item_by_id( $id, $output = OBJECT ){
		global $wpdb;
		
		// Récupération du cache
		if( $db_cache = wp_cache_get( $id, $this->table ) )
			return $db_cache;

		if( in_array( $this->col_{$this->primary_key}['type'], array( 'INT', 'BIGINT' ) ) )
			$query = "SELECT * FROM {$this->wpdb_table} WHERE {$this->wpdb_table}.{$this->primary_key} = %d";
		else
			$query = "SELECT * FROM {$this->wpdb_table} WHERE {$this->wpdb_table}.{$this->primary_key} = %s";
		
		if( ! $item =  $wpdb->get_row( $wpdb->prepare( $query, $id ) ) )
			return;
		
		// Délinéarisation des tableaux
		$item = (object) array_map( 'maybe_unserialize', get_object_vars( $item ) );

		// Mise en cache
		wp_cache_add( $id, $item, $this->table );
		
		if ( $output == OBJECT ) :
			return ! empty( $item ) ? $item : null;
		elseif ( $output == ARRAY_A ) :
			return ! empty( $item ) ? get_object_vars( $item ) : null;
		elseif ( $output == ARRAY_N ) :
			return ! empty( $item ) ? array_values( get_object_vars( $item ) ) : null;
		elseif ( strtoupper( $output ) === OBJECT ) :
			return ! empty( $item ) ? $item : null;
		endif;
	}

	/** == Récupération d'un élément selon un champ et sa valeur == **/
	function get_item_by( $field, $value, $output = OBJECT ){
		global $wpdb;
		
		if( $this->col_prefix_auto )
			$field = $this->col_prefix . $field;
		if( ! in_array( $field, $this->col_names ) )
			return;	

		if( in_array( $this->col_{$field}['type'], array( 'INT', 'BIGINT' ) ) )
			$query = "SELECT * FROM {$this->wpdb_table} WHERE {$this->wpdb_table}.{$field} = %d";
		else
			$query = "SELECT * FROM {$this->wpdb_table} WHERE {$this->wpdb_table}.{$field} = %s";
		
		if( ! $item =  $wpdb->get_row( $wpdb->prepare( $query, $value ) ) )
			return;
		
		// Délinéarisation des tableaux
		$item = (object) array_map( 'maybe_unserialize', get_object_vars( $item ) );

		// Mise en cache
		wp_cache_add( $item->{$this->primary_key}, $item, $this->table );
		
		if ( $output == OBJECT ) :
			return ! empty( $item ) ? $item : null;
		elseif ( $output == ARRAY_A ) :
			return ! empty( $item ) ? get_object_vars( $item ) : null;
		elseif ( $output == ARRAY_N ) :
			return ! empty( $item ) ? array_values( get_object_vars( $item ) ) : null;
		elseif ( strtoupper( $output ) === OBJECT ) :
			return ! empty( $item ) ? $item : null;
		endif;
	}

	/** == Récupération d'une liste d'éléments == **/
	function get_items( $args = array(), $output = OBJECT ){		
		// Bypass	
		if( ! $ids = $this->get_items_ids( $args ) )
			return;		
		// Résultats
		$r = array();
		foreach( $ids as $id )
			$r[] = $this->get_item_by_id( $id, $output );				
		return $r;
	}

	/** == Récupération d'un élément == **/
	function get_item( $args = array(), $output = OBJECT ){
		global $wpdb;
		// Traitement des arguments
		$args['per_page'] 	= 1;
		
		// Bypass	
		if( ! $ids = $this->get_items_ids( $args ) )
			return null;
		$id = current( $ids );					
						
		return $this->get_item_by_id( $id, $output );
	}
	
	/** == Récupération d'une valeur pour un élément == **/
	function get_item_var( $id, $var ){
		global $wpdb;
		
		$col = ( $this->col_prefix_auto ) ? $this->col_prefix.$var : $var;

		if( ( $item = wp_cache_get( $id, $this->table ) ) && isset( $item->{$col} ) )
			return $item->{$col};
		elseif( $var = $wpdb->get_var( $wpdb->prepare( "SELECT {$this->wpdb_table}.{$col} FROM {$this->wpdb_table} WHERE {$this->wpdb_table}.{$this->primary_key} = %d", $id ) ) )
			return maybe_unserialize( $var );
	}
	
	/** == Création d'un élément == **/
	function insert_item( $data = array() ){
		global $wpdb;
		
		if( ! empty( $data[$this->primary_key] ) && ( $item_id = $this->get_item_var( $data[$this->primary_key],  ( $this->col_prefix_auto ? $this->primary_col : $this->primary_key ) ) ) ) :
			unset( $data[$this->primary_key] );
			return $this->update_item( $item_id, $data );
		else :
			$metas = false;
			if( isset( $data['meta'] )  ) :
				$metas = $data['meta'];
				unset( $data['meta'] );
			endif;			
			
			$data = array_map( 'maybe_serialize', $data );
			$wpdb->insert( $this->wpdb_table, $data );
			
			$id = $wpdb->insert_id;
			
			foreach( (array) $metas as $meta_key => $meta_value )
				update_metadata( $this->table, $id, $meta_key, $meta_value );			
			
			return $id;
		endif;
	}
	
	/** == Mise à jour d'un élément == **/
	function update_item( $id, $data = array() ){
		global $wpdb;
		
		$metas = false;
		if( isset( $data['meta'] )  ) :
			$metas = $data['meta'];
			unset( $data['meta'] );
		endif;
	
		$data = array_map('maybe_serialize', $data );
		
		$wpdb->update( $this->wpdb_table, $data, array( $this->primary_key => $id ) );
		
		foreach( (array) $metas as $meta_key => $meta_value )
			update_metadata( $this->table, $id, $meta_key, $meta_value );
		
		return $id;
	}
		
	/** == Suppression d'un élément == **/
	function delete_item( $id ){
		global $wpdb;
		
		return $wpdb->delete( $this->wpdb_table, array( $this->primary_key => $id ), '%d' );
	}
	
	/* = METADONNÉES = */		
	/** == Récupération de la metadonné d'un élément == **/
	function get_item_meta( $id, $key, $single = true ){
		return get_metadata( $this->table .'_meta', $id, $key, $single );
	}
	
	/** == Suppression de métadonnées d'un élément == **/
	function delete_item_metadatas( $id ){
		global $wpdb;
		
		return $wpdb->delete( $this->wpdb_metatable, array( $this->table .'_id' => $id ), '%d' );
	}
	
	/* = CONTRÔLEURS = */
	/** == Traitement des conditions == **/
	function _parse_conditions( $args, $diff = array() ){
		$_args 	= array_diff( array_keys( $args ), array_keys( $diff ) );
		$conditions = array();

		foreach( $_args as $a ) :
			if( in_array( $this->col_prefix.$a, $this->col_names ) ) :
				$col = ( $this->col_prefix_auto ) ? $this->col_prefix.$a : $a;
				$value = $args[$a];
				
				if( $value === 'any' )
					if( isset( $this->col_{$col}['any'] ) )
						$value = $this->col_{$col}['any'];
					else
						continue;					
				
				if( is_string( $value ) ) :
					$conditions[] = "AND  {$this->wpdb_table}.{$col} = '{$value}'";
				elseif( is_numeric( $value ) ) :
					$conditions[] = "AND {$this->wpdb_table}.{$col} = {$value}";
				elseif( is_array( $value ) ) :
					$conditions[] = "AND {$this->wpdb_table}.{$col} IN ('". implode( "', '", $value ) ."')";	
				elseif( is_null( $value ) ) :
					$conditions[] = "AND {$this->wpdb_table}.{$col} IS NULL";	
				endif;	
			endif;
		endforeach;
	
		return implode( ' ', $conditions );
	}
	
	/** == Traitement des exclusions == **/
	function _parse_order( $orderby, $order = 'DESC' ){
		$orderby = ( $this->col_prefix_auto ) ? $this->col_prefix.$orderby : $orderby;
		
		return " ORDER BY {$this->wpdb_table}.{$orderby} {$order}";
	}
	
	/** == Traitement des exclusions == **/
	function _parse_exclude( $exclude ){
		if( ! is_array( $exclude ) )
			$exclude = array( $exclude );
		
		$not_in = implode(',',  array_map( 'absint', $exclude ) );
		return " AND {$this->wpdb_table}.{$this->primary_key} NOT IN ($not_in)";
	}
	
	/** == Traitement des inclusions == **/
	function _parse_include( $include ){
		if( ! is_array( $include ) )
			$include = array( $include );
		
		$in = implode( ',', array_map( 'absint', $include ) );

		return " AND {$this->wpdb_table}.{$this->primary_key} IN ($in)";
	}
}