<?php
/*
Addon Name: Events
Addon URI: http://presstify.com/theme-manager/addons/premium/events
Description: Gestion d'événements
Version: 1.150610
Author: Jordy Manner
Author URI: http://profile.milkcreation.fr/jordy
*/

Class tiFy_Events{
	/* = ARGUMENTS = */
	public 	// Chemins
			$dir,
			$uri,
			
			// Configuration
			$post_types = array(),			
			$by_day_limit	= 2,
			
			// Controleur
			$db;
					
	/* = CONSTRUCTEUR = */
	function __construct(){
		// Définition des chemins	
		$this->dir 	= dirname(__FILE__);
		$this->uri	= plugin_dir_url(__FILE__);
		
		// Configuration		
					
		// Contrôleurs
		$this->db = new tiFy_Events_Db;		
		
		// Actions et filtres Wordpress
		add_action( 'after_setup_theme', array( $this, 'wp_after_setup_theme' ) );
		add_action( 'init', array( $this, 'wp_init' ) );
		add_filter( 'query_vars', array( $this, 'wp_add_query_vars' ), 1 );
		add_action( 'pre_get_posts', array( $this, 'wp_pre_get_posts' ) );
		add_filter( 'posts_clauses', array( $this, 'wp_posts_clauses' ), 99, 2 );
		
		// Actions et Filtres tiFy
		add_action( 'tify_taboox_register_node', array( $this, 'tify_taboox_register_node' ) ); 
		add_action( 'tify_taboox_register_form', array( $this, 'tify_taboox_register_form' ) );
	}
	
	/* = CONFIGURATION = */
	/** == Définition des types de post == **/
	function set_post_types(){
		$defaults = array(
			'taboox_auto'	=> true,				// Déclaration automatique de la boîte de sasie
			'form'			=> 'default',			// Type de saisie default | range
			'by_day_limit'	=> $this->by_day_limit	// Limite de jour consecutifs pour l'affichage en jour séparés : -1 (illimité) | int	
		);
		$post_types = apply_filters( 'tify_events_post_types', array() );
		
		foreach( $post_types as $k => $v )
			if( is_string( $v ) )
				$this->post_types[$v] = $defaults;
			elseif( is_array( $v ) )
				$this->post_types[$k] = wp_parse_args( $v, $defaults );
	}
	
	/* = CONTROLEURS = */
	/** == Récupération des types de posts == **/
	function get_post_types(){
		// Bypass
		if( ! is_array( $this->post_types ) )
			return array();
		
		return array_keys( $this->post_types );		
	}
	
	/** == Vérifie si le type de post est valide  == **/
	function is_post_type( $post_type ){
		return in_array( $post_type, $this->get_post_types() );
	}
	
	/** == Récupération d'une option de type de post  == **/
	function get_post_type_option( $post_type, $option ){
		// Bypass
		if( ! $this->is_post_type( $post_type ) )
			return;
		
		if( isset( $this->post_types[$post_type][$option] ) )
			return $this->post_types[$post_type][$option];
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation du thème == **/
	function wp_after_setup_theme(){
		$this->set_post_types();
	}

	/** == == **/
	function wp_add_query_vars($aVars) {
	  $aVars[] = 'tyevshow'; // all | uniq
	  $aVars[] = 'tyevfrom'; 
	  $aVars[] = 'tyevto';
	  
	  return $aVars;
	}

	/** == == **/
	function wp_pre_get_posts( $query ){
			
	}

	/** == == **/	
	function wp_posts_clauses( $pieces, $query ){	
		//Bypass	
		if( is_admin() && ! defined( 'DOING_AJAX' ) )
			return $pieces;		
		
		if( ! $post_types = $query->get( 'post_type' ) )
			return $pieces;
		
		// Traitement des types de post		
		if( ! is_array( $post_types ) )
			$post_types = array( $post_types );
		/// La requête ne doit contenir des types de post déclarés		
		if( in_array( 'any', $post_types ) )
			return $pieces;			
		if( array_diff( $post_types, $this->get_post_types() ) )
			return $pieces;

		global $wpdb;
		extract( $pieces );	
	
		// Récupération des arguments de contruction de la requête
		$show 	= ( ( $_show = $query->get( 'tyevshow' ) ) && in_array( $_show, array( 'all', 'uniq' ) ) ) ? $_show : 'uniq';
		$from 	= ( $_from = $query->get( 'tyevfrom' ) ) ? $_from : current_time( 'mysql' );
		$to 	= ( $_to = $query->get( 'tyevto' ) ) ? $_to : false;
			
		$fields .= ", tify_events.event_id, tify_events.event_start_datetime, tify_events.event_end_datetime";
		
		$join .= " INNER JOIN {$this->db->wpdb_table} as tify_events ON ( $wpdb->posts.ID = tify_events.event_post_id )";  	

		if( $show === 'uniq' ) :
			$inner_where  = "SELECT MIN( event_start_datetime ) FROM {$this->db->wpdb_table} WHERE event_post_id = $wpdb->posts.ID";
			$inner_where .= " AND event_end_datetime >= '". $from ."'";
			if( $to )
				$inner_where .= " AND event_start_datetime <= '". $to ."'";			
			$where .= " AND tify_events.event_start_datetime IN ( $inner_where )";
		else :		
			$where .= " AND tify_events.event_end_datetime >= '". $from ."'";
			if( $to )
				$where .= " AND event_start_datetime <= '". $to ."'";	
		endif;
		
		$orderby = "tify_events.event_start_datetime ASC";
		$groupby = false;
		
		$_pieces = array( 'where', 'groupby', 'join', 'orderby', 'distinct', 'fields', 'limits' );
		
		return compact ( $_pieces );
	}
	
	/* = ACTIONS ET FILTRES TIFY = */
	/** == == **/
	function tify_taboox_register_node(){		
		foreach( (array) $this->post_types as $post_type => $args )
			if( $args['taboox_auto'] )
				tify_taboox_register_node_post( 
					$this->get_post_types( ), 
					array( 
						'id' 		=> 'tify_events',
						'title' 	=> __( 'Dates', 'tify' ), 
						'cb' 		=> 'tiFy_Events_Taboox' 
					) 
				);
	}
	/** == Déclaration de la taboox de saisie des dates == **/
	function tify_taboox_register_form(){
		tify_taboox_register_form( 'tiFy_Events_Taboox', $this );
	}	
}
global $tify_events;
$tify_events = new tiFy_Events;

/* == GESTION DES DONNEES EN BASE = */
class tiFy_Events_Db extends tiFy_db{
	/* = CONSTRUCTEUR = */	
	function __construct( ){
		// Définition des arguments
		$this->table 		= 'tify_events';
		$this->col_prefix	= 'event_'; 
		$this->has_meta		= true;
		$this->cols			= array(
			'id' 			=> array(
				'type'				=> 'BIGINT',
				'size'				=> 20,
				'unsigned'			=> true,
				'auto_increment'	=> true
			),
			'post_id' 			=> array(
				'type'			=> 'BIGINT',
				'size'			=> 20,
				'unsigned'		=> true,
				'key'			=> 'event_post_id'
			),
			'start_datetime'		=> array(
				'type'			=> 'DATETIME',
				'default'		=> '0000-00-00 00:00:00'
			),
			'end_datetime'		=> array(
				'type'			=> 'DATETIME',
				'default'		=> '0000-00-00 00:00:00'
			)
		);		
		parent::__construct();				
	}	
}

/* = TABOOXES = */
/** == Taboox de saisie des dates == **/
class tiFy_Events_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public	$name = '',
			// contrôleur
			$master;
			
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Events $master ){
		$this->master = $master;
			
		parent::__construct(
			// Options
			array(
				'environnements'	=> array( 'post' ),
				'dir'				=> dirname( __FILE__ ),
				'instances'  		=> 1
			)
		);
		
		// Actions et Filtres Wordpress
		add_action( 'post_edit_form_tag', array( $this, 'wp_post_edit_form_tag' ) );
		add_action( 'wp_ajax_tify_events_display_preview', array( $this, 'wp_ajax_display_preview' ) );		
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == == **/
	function wp_post_edit_form_tag( $post ){
		// Bypass
		if( ! in_array( $post->post_type, $this->master->get_post_types() ) )
			return;
		echo " autocomplete=\"off\" ";
	}
	/** == == **/
	function wp_ajax_display_preview(){
		$start = $_POST['start'];
		$end = $_POST['end'];
		$post_type = $_POST['post_type'];
		$by_day_limit = $this->master->get_post_type_option( $post_type, 'by_day_limit' );		
		
		$output = "";
		if( $dates = tify_events_get_dates( $start, $end, $by_day_limit ) )
			foreach( $dates as $date )
				$output .= tify_events_date_display( $date, false ) ."\n";		
		
		echo $output;	
		exit;
	}
	
	
	/* = VUE = */
	/** == Mise en file des scripts == **/
	function enqueue_scripts(){
		tify_controls_enqueue( 'switch' );
		tify_controls_enqueue( 'touch_time' );
		tify_controls_enqueue( 'dynamic_inputs' );
		wp_enqueue_style( 'tify_events', $this->master->uri .'admin.css', array(), 150610 );
		wp_enqueue_script( 'tify_events', $this->master->uri .'admin-default.js', array( 'jquery', 'momentjs' ), 150610, true );
		//wp_enqueue_script( 'tify_events', $this->master->uri .'admin.js', array( 'jquery', 'momentjs' ), 150610, true );
		wp_localize_script( 
			'tify_events', 
			'tify_events', 
			array( 
				'date_range_error' => __( 'La date de début est supérieur à la date de fin', 'tify' )
			)
		);	
	}
	
	/** == Formulaire de saisie == **/
	function form( $args = array() ){
		$values = $this->master->db->get_items( array( 'post_id' => $this->post->ID, 'order' => 'ASC', 'orderby' => 'start_datetime' ), ARRAY_A );
		
		// Default
		$values = $this->parse_values( $values );
		$sample_html = $this->sample_default();	
				
		$args = array( 
			'sample_html' 				=> $sample_html, 
			'name' 						=> 'tify_event', 
			'values' 					=> $values
		);
		
		$args['default'] = array(
			'event_start_date' 		=> date( 'Y-m-d', current_time( 'timestamp' ) ),
			'event_start_time' 		=> date( 'H:i:00', current_time( 'timestamp' ) ),
			'event_end_time' 		=> date( 'H:i:00', current_time( 'timestamp' ) ),
			'event_end_date' 		=> date( 'Y-m-d', current_time( 'timestamp' ) ),
			'event_post_id' 		=> $this->post->ID, 
			'event_id' 				=> 0 
		);
	?>
		<div class="tify_events-taboox">
			<?php tify_control_dynamic_inputs( $args ); ?>
		</div>
	<?php
	}
	
	/** == == **/
	function sample_default(){
		$sample_html  = "";
		$sample_html .= "<div class=\"col\">";					
		$sample_html .= "\t<table>\n";
		$sample_html .= "\t\t<tbody>\n";
		$sample_html .= "\t\t\t<tr>\n";
		$sample_html .= "\t\t\t\t<th scope=\"row\"><label>". __( 'Du', 'tify' ) ."</label></th>\n";
		$sample_html .= "\t\t\t\t<td>";
		$sample_html .= tify_control_touch_time( 
							array( 
								'name' 				=> '%%name%%[%%index%%][event_start_date]', 
								'value' 			=> '%%value%%[event_start_date]',
								'container_id' 		=> 'tify_event_start_date-wrapper-%%index%%',
								'container_class' 	=> 'tify_event_start_date-wrapper',
								'id' 				=> 'tify_event_start_date-%%index%%',
								'type'				=> 'date', 
								'echo' 				=> 0,
								'debug'				=> false
							) 
						);
		$sample_html .= "\t\t\t\t</td>\n";
		$sample_html .= "\t\t\t</tr>\n";
		$sample_html .= "\t\t</tbody>\n";
		$sample_html .= "\t</table>\n";
		
		$sample_html .= "\t<table>\n";
		$sample_html .= "\t\t<tbody>\n";
		$sample_html .= "\t\t\t<tr>\n";
		$sample_html .= "\t\t\t\t<td class=\"label\"><label>". __( 'De', 'tify' ) ."</label></td>\n";
		$sample_html .= "\t\t\t\t<td class=\"hour\">";
		$sample_html .= tify_control_touch_time( 
							array( 
								'name' 				=> '%%name%%[%%index%%][event_start_time]', 
								'value' 			=> '%%value%%[event_start_time]',
								'container_id' 		=> 'tify_event_start_time-wrapper-%%index%%',
								'container_class' 	=> 'tify_event_start_time-wrapper',
								'id' 				=> 'tify_event_start_time-%%index%%',
								'type'				=> 'time', 
								'echo' 				=> 0,
								'debug'				=> false
							) 
						);
		$sample_html .= "\t\t\t\t</td>\n";
		$sample_html .= "\t\t\t\t<td class=\"label\"><label>". __( 'A', 'tify' ) ."</label></th>\n";
		$sample_html .= "\t\t\t\t<td class=\"hour\">";
		$sample_html .= tify_control_touch_time( 
							array( 
								'name' 				=> '%%name%%[%%index%%][event_end_time]', 
								'value' 			=> '%%value%%[event_end_time]',
								'container_id' 		=> 'tify_event_end_time-wrapper-%%index%%',
								'container_class' 	=> 'tify_event_end_time-wrapper',
								'id' 				=> 'tify_event_end_time-%%index%%',
								'type'				=> 'time', 
								'echo' 				=> 0,
								'debug'				=> false
							) 
						);
		$sample_html .= "\t\t\t\t</td>\n";
		$sample_html .= "\t\t\t</tr>\n";
		$sample_html .= "\t\t</tbody>\n";
		$sample_html .= "\t</table>\n";
		
		$sample_html .= "\t<table>\n";
		$sample_html .= "\t\t<tbody>\n";
		$sample_html .= "\t\t\t<tr>\n";
		$sample_html .= "\t\t\t\t<th scope=\"row\"><label>";
		$sample_html .= "\t\t\t\t\t". __( 'Jusqu\'au', 'tify' ) ."\n";
		$sample_html .= "\t\t\t\t</label></th>\n";
		$sample_html .= "\t\t\t\t<td>";		
		$sample_html .= "\t\t\t\t\t". 
						tify_control_touch_time( 
							array( 
								'name' 				=> '%%name%%[%%index%%][event_end_date]', 
								'value' 			=> '%%value%%[event_end_date]',
								'container_id' 		=> 'tify_event_end_date-wrapper-%%index%%',
								'container_class' 	=> 'tify_event_end_date-wrapper',
								'id' 				=> 'tify_event_end_date-%%index%%',
								'type'				=> 'date',
								'echo' 				=> 0,
								'debug'				=> false
							) 
						) .
						"\n";
		$sample_html .= "\t\t\t\t</td>\n";
		$sample_html .= "\t\t\t</tr>\n";
		$sample_html .= "\t\t</tbody>\n";
		$sample_html .= "\t</table>\n";
		$sample_html .= "</div>";
		$sample_html .= "\t<div class=\"col preview\"><strong>". __( 'Prévisualisation :', 'tify' ) ."</strong><textarea readonly=\"readonly\" autocomplete=\"off\"></textarea></div>\n";
		$sample_html .= "<input type=\"hidden\" name=\"%%name%%[%%index%%][event_id]\" value=\"%%value%%[event_id]\">\n";
		$sample_html .= "<input type=\"hidden\" name=\"%%name%%[%%index%%][event_post_id]\" value=\"%%value%%[event_post_id]\">\n";
		
		return $sample_html;
	}
	
	/** == == **/
	function sample_daterange( $start_default, $end_default ){
		$sample_html  = "";					
		$sample_html .= "<table class=\"form-table\">\n";
		$sample_html .= "\t<tbody>\n";
		$sample_html .= "\t\t<tr>\n";
		$sample_html .= "\t\t\t<th scope=\"row\"><label>". __( 'Date de début', 'tify' ) ."</label></th>\n";
		$sample_html .= "\t\t\t<td>";
		$sample_html .= tify_control_touch_time( 
							array( 
								'name' 				=> '%%name%%[%%index%%][event_start_datetime]', 
								'value' 			=> '%%value%%[event_start_datetime]',
								'container_id' 		=> 'tify_event_start_datetime-wrapper-%%index%%',
								'container_class' 	=> 'tify_event_start_datetime-wrapper',
								'id' 				=> 'tify_event_start_datetime-%%index%%', 
								'echo' 				=> 0,
								'debug'				=> false
							) 
						);
		$sample_html .= "</td>\n";
		$sample_html .= "\t\t</tr>\n";
		$sample_html .= "\t\t<tr>\n";
		$sample_html .= "\t\t\t<th scope=\"row\"><label>";
		$sample_html .= "\t\t\t\t". __( 'Date de fin', 'tify' ) ."\n";
		$sample_html .= "\t\t\t</label></th>\n";
		$sample_html .= "\t\t\t<td>";		
		$sample_html .= "\t\t\t\t". 
						tify_control_touch_time( 
							array( 
								'name' 				=> '%%name%%[%%index%%][event_end_datetime]', 
								'value' 			=> '%%value%%[event_end_datetime]',
								'container_id' 		=> 'tify_event_end_datetime-wrapper-%%index%%',
								'container_class' 	=> 'tify_event_end_datetime-wrapper',
								'id' 				=> 'tify_event_end_datetime-%%index%%',
								'echo' 				=> 0,
								'debug'				=> false
							) 
						) .
						"\n";
		$sample_html .= "</td>\n";
		$sample_html .= "\t\t</tr>\n";
		$sample_html .= "\t</tbody>\n";
		$sample_html .= "</table>\n";		
		$sample_html .= "<input type=\"hidden\" name=\"%%name%%[%%index%%][event_id]\" value=\"%%value%%[event_id]\">\n";
		$sample_html .= "<input type=\"hidden\" name=\"%%name%%[%%index%%][event_post_id]\" value=\"%%value%%[event_post_id]\">\n";
		
		return $sample_html;
	}
		
	/** == Sauvegarde des posts == **/
	public function save_post( $post_id, $post ){
		// Suppression des dates
		if( $exists = $this->master->db->get_items_ids( array( 'post_id' => $post_id ) ) ) :
			$save = array();
			if( isset( $_POST['tify_event'] ) )
				foreach( $_POST['tify_event'] as $event )
					array_push( $save, $event['event_id'] );
				
			foreach( $exists as $id )
				if( empty( $save ) )
					$this->master->db->delete_item( $id );
				elseif( ! in_array( $id, $save ) )
					$this->master->db->delete_item( $id );
		endif;
		// Enregistrement des dates
		if( ! empty( $_POST['tify_event'] ) ) :
			$datas = $this->parse_datas( $_POST['tify_event'] );	
			
			foreach( $datas as $id => $e ) :								
				$start = new DateTime( $e['event_start_datetime'] ); $end = new DateTime( $e['event_end_datetime'] );

				if( $start > $end ) :
					$e['event_end_datetime'] = $start->format( 'Y-m-d' ) .' '. $end->format( 'H:i:s' );
					$end = new DateTime( $e['event_end_datetime'] );
				endif;
				if( $start->format( 'Hi' ) > $end->format( 'Hi' ) ) :
					$end->add( new DateInterval( 'P1D' ) );
					$e['event_end_datetime'] = $end->format( 'Y-m-d H:i:s' );
				endif;
				
				$this->master->db->insert_item( $e );
			endforeach;
		endif;
	}
	
	/** == Traitement des données == **/
	public function parse_values( $values ){
		foreach( (array) $values as $k => $value ) :
			$s = new DateTime( $value['event_start_datetime'] );
			if( $s->format('Y') < 0 )
				$values[$k]['event_start_date'] = date( 'Y-m-d', current_time( 'timestamp' ) );
			else	
				$values[$k]['event_start_date'] = $s->format( 'Y-m-d' );
			$values[$k]['event_start_time'] = $s->format( 'H:i:s' );			
			unset( $values[$k]['event_start_datetime'] );
			
			$e = new DateTime( $value['event_end_datetime'] );
			if( $e->format( 'Hi' ) < $s->format( 'Hi' ) )
					$e->sub( new DateInterval( 'P1D' ) );			
			$values[$k]['event_end_date'] = $e->format( 'Y-m-d' );
			$values[$k]['event_end_time'] = $e->format( 'H:i:s' ); 
			unset( $values[$k]['event_end_datetime'] );
		endforeach;
			
		return $values;	
	}
	
	/** == Traitement des données == **/
	public function parse_datas( $datas ){
		foreach( $datas as $k => &$data ) :
			if( isset( $data['event_start_date'] ) && isset( $data['event_start_time'] ) ) :
				$data['event_start_datetime'] = $data['event_start_date'] ." ". $data['event_start_time']; 
				unset( $datas[$k]['event_start_date'] ); unset( $datas[$k]['event_start_time'] );
			endif;
			if( isset( $data['event_end_date'] ) && isset( $data['event_end_time'] ) ) :
				$data['event_end_datetime'] = $data['event_end_date'] ." ". $data['event_end_time'];
				unset( $datas[$k]['event_end_date'] ); unset( $datas[$k]['event_end_time'] ); 
			endif;
			if( empty( $data['event_start_datetime'] ) || empty( $data['event_end_datetime'] ) ) :
				unset( $datas[$k] ); continue;
			endif;			
		endforeach;
			
		return $datas;	
	}
}

/* = GENERAL TEMPLATE = */
/** == Récupérère les dates d'un événement == **/
function tify_events_get_the_dates( $post_id = 0, $query_args = array() ){
	global $tify_events;
	
	$post_id = absint( $post_id );
	if ( ! $post_id )
		$post_id = get_the_ID();
	
	// Bypass
	if( ! $post_id )
		return;	
	
	// Traitement des arguments de requête
	$defaults = array(
		'orderby' 	=> 'start_datetime',
		'order'		=> 'ASC'
	);
	$query_args = wp_parse_args( $defaults, $query_args );	
	$query_args['post_id'] = $post_id;
	
	// Récupération des événements
	if( ! $res = $tify_events->db->get_items( $query_args ) )
		return;
	
	// Formatage du résultat
	$the_dates = array(); $k = 0;
	foreach( $res as $r ) :
		$s = new DateTime( $r->event_start_datetime );
		$e = new DateTime( $r->event_end_datetime );
		if( $e->format( 'Hi' ) < $s->format( 'Hi' ) ) :
			$e->sub( new DateInterval( 'P1D' ) );
			$r->event_end_datetime = $e->format( 'Y-m-d H:i:s' );
		endif;
		if( $dates = tify_events_get_dates( $r->event_start_datetime, $r->event_end_datetime, $tify_events->get_post_type_option( get_post_type( $post_id ), 'by_day_limit' ) ) ) :
			foreach( (array) $dates as $date ) :
				$the_dates[ $k ] = $date;
				$k++;
			endforeach;
		endif;
	endforeach;
	
	return $the_dates;
}

/** == Récupération des dates relatives à un créneau == **/
function tify_events_get_dates( $start, $end, $by_day_limit = 2 ){
	$dates = array(); $k = 0;
	
	$s = new DateTime( $start ); $e = new DateTime( $end );
		
	if( $s->format( 'Ymd' ) === $e->format( 'Ymd' ) ) :
		$dates[$k]['start_date'] = $s->format( 'Y-m-d' );
		$dates[$k]['end_date'] = false;
		
		if( $s->format( 'Hi' ) == $e->format( 'Hi' ) ) :
			$dates[$k]['start_time'] = $s->format( 'H:i:s' ); 
			$dates[$k]['end_time'] = false;
		else :
			$dates[$k]['start_time'] = $s->format( 'H:i:s' );
			$dates[$k]['end_time'] = $e->format( 'H:i:s' );
		endif;	
	elseif( $s->format( 'Ymd' ) > $e->format( 'Ymd' ) ) :
		$dates[$k]['start_date'] = $s->format( 'Y-m-d' );
		$dates[$k]['end_date'] = false;		
		
		if( $s->format( 'Hi' ) == $e->format( 'Hi' ) ) :
			$dates[$k]['start_time'] = $s->format( 'H:i:s' ); 
			$dates[$k]['end_time'] = false;
		else :
			$dates[$k]['start_time'] = $s->format( 'H:i:s' );
			$dates[$k]['end_time'] = $e->format( 'H:i:s' );
		endif;
	else :
		$sdiff = new DateTime( $s->format( 'Y-m-d' ) );
		$ediff = new DateTime( $e->format( 'Y-m-d' ) );
		$diff = $sdiff->diff( $ediff );
		if( $by_day_limit == -1 )
			$by_day_limit = $diff->days;
		
		if( $diff->days && $diff->days <= $by_day_limit ) :			
			foreach( range( 0, $diff->days, 1 ) as $n ) :
				if( $n )
					$s->add( new DateInterval( 'P1D' ) );
				$dates[$n]['start_date'] = $s->format( 'Y-m-d' );
				$dates[$n]['end_date'] = false;
				
				if( $s->format( 'Hi' ) == $e->format( 'Hi' ) ) :
					$dates[$n]['start_time'] = $s->format( 'H:i:s' ); 
					$dates[$n]['end_time'] = false;
				else :
					$dates[$n]['start_time'] = $s->format( 'H:i:s' );
					$dates[$n]['end_time'] = $e->format( 'H:i:s' );
				endif;
			endforeach;
		else :
			$dates[$k]['start_date'] = $s->format( 'Y-m-d' );
			$dates[$k]['end_date'] = $e->format( 'Y-m-d' );
			
			if( $s->format( 'Hi' ) == $e->format( 'Hi' ) ) :
				$dates[$k]['start_time'] = $s->format( 'H:i:s' ); 
				$dates[$k]['end_time'] = false;
			else :
				$dates[$k]['start_time'] = $s->format( 'H:i:s' );
				$dates[$k]['end_time'] = $e->format( 'H:i:s' );
			endif;
		endif;
	endif;
	
	return $dates;
}

/** == Affichage d'une date == **/
function tify_events_date_display( $date, $echo = true ){		
	$output = "";
	
	if( ! $output = apply_filters( 'tify_events_date_display', '', $date ) ) :
		if( ! $date['end_date'] )
			$output .= sprintf( __( 'le %s', 'tify' ), mysql2date( 'l d M', $date['start_date'] ) );
		elseif( (int) substr( $date['end_date'], 0, 4 ) > (int) substr( $date['start_date'], 0, 4 ) )
			$output .= sprintf( __( 'du %s au %s', 'tify' ), mysql2date( 'l d M Y', $date['start_date'] ), mysql2date( 'l d M Y', $date['end_date'] ) );
		else
			$output .= sprintf( __( 'du %s au %s', 'tify' ), mysql2date( 'l d M', $date['start_date'] ), mysql2date( 'l d M', $date['end_date'] ) );
		
		if( ! $date['end_time'] ) :
			$output .= sprintf( __( ' à %s', 'tify' ), preg_replace( '/^(\d{2}):(\d{2}):(\d{2})$/', '$1h$2', $date['start_time'] ) );
		else :
			$output .= sprintf( __( ' de %s à %s', 'tify' ), preg_replace( '/^(\d{2}):(\d{2}):(\d{2})$/', '$1h$2', $date['start_time'] ), preg_replace( '/^(\d{2}):(\d{2}):(\d{2})$/', '$1h$2', $date['end_time'] ) );
		endif;
	endif;
	
	if( $echo )
		echo $output;
	
	return $output;
}

/* = POST TEMPLATE = */
/** == == **/
function get_the_tify_events_date( $post = 0, $by_day_limit = null ){
	global $tify_events;
	
	// Bypass
	if( ! $post = get_post( $post ) )
		return;	
	if( ! $tify_events->is_post_type( $post->post_type ) )
		return;

	if( is_null( $by_day_limit ) )
		$by_day_limit = $tify_events->get_post_type_option( $post->post_type, 'by_day_limit' );
		
	if( $dates = tify_events_get_dates( $post->event_start_datetime, $post->event_end_datetime, $by_day_limit ) ) :
		$_dates = array();
		foreach( $dates as $date )
			$_dates[] = tify_events_date_display( $date, false );
		
		return implode( ', ', $_dates );
	endif;	
}

/** == == **/
function get_the_tify_events_dates( $post = 0 ){
	global $wpdb, $tify_events;
	
	// Bypass
	if( ! $post = get_post( $post ) )
		return;	
	if( ! $tify_events->is_post_type( $post->post_type ) )
		return;

	$query = "SELECT MIN( event_start_datetime ) AS start_datetime, MAX( event_end_datetime ) AS end_datetime";
	$query .= " FROM $wpdb->tify_events";
	$query .= " WHERE 1";
	$query .= " AND event_post_id = $post->ID";
	
	$r = $wpdb->get_row( $query );
	$dates = tify_events_get_dates( $r->start_datetime, $r->end_datetime, 0 );
	
	return tify_events_date_display( current( $dates ), false );
}