<?php
/* = TOPICS = */
/**
 * Récupération des sujets liés à un forum
 */ 
function mkforums_get_topics_for_forum( $args = array() ){
	global $post;
		
	$defaults = array(
		'attached_forum' => 0,
		'orderby' => 'menu_order',
		'order' => 'ASC'
	);
	$query_args = wp_parse_args( $args, $defaults );
	
	if(	empty( $query_args['attached_forum'] ) && isset( $post->post_type ) && ( $post->post_type == 'mkforums' )  )
		$query_args['attached_forum'] = $post->ID;

	// Arguments obligatoires
	$query_args['post_type'] = 'mktopics';
	$query_args['meta_key'] = '_mkforums_forum_attachment_id';
	$query_args['meta_value'] = $query_args['attached_forum'];
	
	$query_topics = new WP_Query;
	$topics = $query_topics->query( $query_args );
	
	if( $query_topics->post_count )
		return $topics;	
}

/* = CONTRIBUTION = */
/**
 * 
 */
function mkforums_pre_get_comments( &$wpcomment ){
	$args = array();	
	if( !empty( $_REQUEST['post_type']) && $_REQUEST['post_type']==='mktopics' )	
		$args['post_type'] = 'mktopics';

	$wpcomment->query_vars = wp_parse_args( $args, $wpcomment->query_vars );
}
add_action( 'pre_get_comments', 'mkforums_pre_get_comments' );

/**
 * 
 */
function mkforums_post_contrib_handle(){
	if( get_query_var('mkforums_post_contrib') )
		exit;
}
add_action( 'init', 'mkforums_post_contrib_handle');

/** == QUERY == **/
/**
 * Ajout d'une variable à la requête WP_Query
 * pour le filtrage par forum
 */
function mkforums_add_query_vars($aVars) {
  $aVars[] .= 'mkforum_id';
  $aVars[] .= 'mkforums_post_contrib';
  return $aVars;
}
add_filter('query_vars', 'mkforums_add_query_vars');

/**
 * Colonne personnalisées des sujets de forums.
 */
function mkforums_custom_columns( $columns ){
	$end = array_slice($columns, 2);	
	array_splice($columns, 2);
	$columns['mktopics_forum'] =  __( 'Forum associé', 'tify' );	
	$columns += $end;
	
	return $columns;
}
add_filter( 'manage_edit-mktopics_columns', 'mkforums_custom_columns' );

/**
 * 
 */
function mkforums_custom_rows( $column, $post_id ){
	if( $column == 'mktopics_forum' ) :
		if( $forum_parent = get_post_meta( $post_id, '_tify_forum_parent', true ) )
			printf( '%s', get_the_title( $forum_parent ) );
		else 	
			_e( 'Aucun forum associé', 'tify' );
	endif;
}
add_action( 'manage_mktopics_posts_custom_column', 'mkforums_custom_rows', '', 2);
	
/**
 * 
 */
function mkforums_column_filters(){
	global $post_type;

	// Bypass
	if( !isset($post_type) || $post_type != 'mktopics' )
		return;
	
	$selected = ! empty( $_REQUEST['mkforum_id'] )? $_REQUEST['mkforum_id'] : 0;

	$show_option_none = __( 'All Forums', 'tify');
	$echo = false;
	$post_type = 'mkforums';
	
	$dropdown_args = array( 'selected', 'show_option_none', 'echo', 'post_type' );
	$args = compact( $dropdown_args );
	
	$output = wp_dropdown_pages( $args );
	
	echo $output;	
}
add_action( 'restrict_manage_posts', 'mkforums_column_filters'  );

/**
 * Filtrage des sujets par forum
 */
function mkforums_topics_table_filter_columns( $vars ) {
	if ( !empty( $vars['mkforum_id'] ) ) {
		$vars = array_merge( $vars, array(
			'meta_key' => '_mkforums_forum_attachment_id',
			'meta_value' => $vars['mkforum_id'],
			'orderby' => 'meta_value'
		) );
	}
	return $vars;
}
add_filter( 'request', 'mkforums_topics_table_filter_columns' ); 
