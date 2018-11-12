<?php
/** 150711 **/
function register_taboox_option( $hookname, $args = array() ){
	_deprecated_function( __FUNCTION__, '150711', 'tify_taboox_register_box_option()' );
	tify_taboox_register_box_option( $hookname, $args );
}
function taboox_option_add_node( $hookname, $args = array() ){
	_deprecated_function( __FUNCTION__, '150711', 'tify_taboox_register_node_option()' );
	tify_taboox_register_node_option( $hookname, $args );
}
function register_taboox_post( $hookname, $args = array() ){
	_deprecated_function( __FUNCTION__, '150711', 'tify_taboox_register_box_post()' );
	tify_taboox_register_box_post( $hookname, $args );	
}
function taboox_post_add_node( $hookname, $args = array() ){
	_deprecated_function( __FUNCTION__, '150711', 'tify_taboox_register_node_post()' );
	tify_taboox_register_node_post( $hookname, $args );	
}
function register_taboox_taxonomy( $hookname, $args = array() ){
	_deprecated_function( __FUNCTION__, '150711', 'tify_taboox_register_box_taxonomy()' );
	tify_taboox_register_box_taxonomy( $hookname, $args );		
}
function taboox_taxonomy_add_node( $hookname, $args = array() ){
	_deprecated_function( __FUNCTION__, '150711', 'tify_taboox_register_node_taxonomy()' );
	tify_taboox_register_node_taxonomy( $hookname, $args );
}
function tify_register_taboox( $form_class, $passed_args = array() ){
	_deprecated_function( __FUNCTION__, '150711', 'tify_taboox_register_form()' );	
	tify_taboox_register_form( $form_class, $passed_args );
}
add_action( 'init', '_deprecated_taboox_init', 1 );
function _deprecated_taboox_init(){
	global $wp_filter;
	if( !isset( $wp_filter['tify_taboox_init'] ) )
		return;
	
	foreach ( (array) current($wp_filter['tify_taboox_init']) as $the_ ) :
		if ( ! is_null( $the_['function'] ) ) :
			_deprecated_function( $the_['function'], '150711', 'add_action( "tify_taboox_register_form", "'. $the_['function']  .'" )' );
			add_action( 'tify_taboox_register_form', $the_['function'] );
		endif;
	endforeach;
}		





/** ANTERIEUR **/
/**
 * OPTIONS
 */
/**
 * Déclaration d'une boîte à onglet d'option
 */
function register_option_box( $option_page, $nodes, $args = array() ){
	_deprecated_function( __FUNCTION__, '1.5.1', 'register_taboox_option()' );
	
	
	global $tiFy_tabooxes_master;
	
	foreach( $nodes as $node )
		$tiFy_tabooxes_master->option->add_node( 'settings_page_'. $option_page, $node );
	
	$tiFy_tabooxes_master->option->set_box( 
		'settings_page_'. $option_page,
		array( 
			'title'		=> isset( $args['title'] ) ? $args['title'] : '',
			'scripts'	=> isset( $args['scripts'] ) ? $args['scripts'] : '',
		)
	);
	
	add_settings_section( 'settings_page_'. $option_page, null, array( $tiFy_tabooxes_master->option, 'box_render' ), $option_page );
}

/**
 * POSTS
 */
/**
 * Déclaration des postbox
 */
function register_postbox( $box, $object_type, $args ){
	global $tiFy_tabooxes_master;
	
	foreach( (array) $object_type as $post_type ) :
		// Bypass

		if( ! $post_type_obj = get_post_type_object( $post_type ) )
			continue;
			$tiFy_tabooxes_master->post->set_box( 
				$post_type,
				array( 
					'title'		=> isset( $args['label'] ) ? $args['label'] : '',
					'scripts'	=> isset( $args['boxes'] ) ? $args['boxes'] : '',
				)
			);
		foreach( $args['sections'] as $section ) :
			/// Niveau 1
			$tiFy_tabooxes_master->post->add_node( 
				$post_type,
				array(
					'id' 	=> $section['node'],
					'title'	=> $section['label'],
					'cb'	=> isset( $section['cb'] ) ? $section['cb'] : '',
					'args'	=> isset( $section['args'] ) ? $section['args'] : ''
				)
			);
			/// Niveau 2
			if( empty( $section['sections'] ) ) continue;
			foreach( (array) $section['sections'] as $sub ) :
				$tiFy_tabooxes_master->post->add_node( 
					$post_type,
					array(
						'id' 	=> $sub['node'],
						'title'	=> $sub['label'],
						'parent'=> $section['node'],
						'cb'	=> isset( $sub['cb'] ) ? $sub['cb'] : '',
						'args'	=> isset( $sub['args'] ) ? $sub['args'] : ''
					)
				);
				/// Niveau 3 et la recursivité mon con ? tu connais ?!
				if( empty( $sub['sections'] ) ) continue;
				foreach( (array) $sub['sections'] as $subsub ) :
					$tiFy_tabooxes_master->post->add_node( 
						$post_type,
						array(
							'id' 	=> $subsub['node'],
							'title'	=> $subsub['label'],
							'parent'=> $sub['node'],
							'cb'	=> isset( $subsub['cb'] ) ? $subsub['cb'] : '',
							'args'	=> isset( $subsub['args'] ) ? $subsub['args'] : ''
						)
					);
				endforeach;	
			endforeach;
		endforeach;
	endforeach;
}

/**
 * Ajout d'une entrée de saisie
 */
function mkpbx_add_section( $args ){
	global $tiFy_tabooxes_master;
	
	foreach( $args['post_type'] as $post_type )
		$tiFy_tabooxes_master->post->add_node( 
			$post_type,
			array(
				'id' 	=> $args['node'],
				'title'	=> $args['label'],
				'parent'=> isset( $args['parent'] ) ? $args['parent'] : '',
				'cb'	=> isset( $args['cb'] ) ? $args['cb'] : '',
				'args'	=> isset( $args['args'] ) ? $args['args'] : ''
			)
		);
}

/**
 * TAXONOMY
 */
/**
 * Déclaration d'un boîte à onglet d'option
 */
function register_taxonomy_box( $taxonomy, $nodes, $args = array() ){
	global $tiFy_tabooxes_master;
	
	foreach( $nodes as $node )
		$tiFy_tabooxes_master->taxonomy->add_node( 'edit-' .$taxonomy, $node );
	
	$tiFy_tabooxes_master->taxonomy->set_box( 
		'edit-' .$taxonomy,
		array( 
			'title'		=> isset( $args['title'] ) ? $args['title'] : '',
			'scripts'	=> isset( $args['scripts'] ) ? $args['scripts'] : '',
		)
	);
	
	add_action( $taxonomy .'_edit_form', array( $tiFy_tabooxes_master->taxonomy, 'box_render' ), null, 2);
	add_action( 'edited_'. $taxonomy, 'taxonomy_box_save_metadata', null, 2 );
} 


/**
 * ENREGISTREMENT DES DONNÉES
 */
/**
 *  Enregistrement des métadonnées de post en mode single
 */
function mkpbx_save_post_single_metadata( $post_id, $post ){
	// Contrôle s'il s'agit d'une routine de sauvegarde automatique.	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;
	// Contrôle s'il s'agit d'une routine de sauvegarde automatique.	
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
      return;	

	//Bypass
	if( !isset($_POST['post_type']) )
		return;
		
	// Contrôle des permissions
  	if ( 'page' == $_POST['post_type'] )
    	if ( !current_user_can( 'edit_page', $post_id ) )
        	return;
  	else
    	if ( !current_user_can( 'edit_post', $post_id ) )
        	return;
			
	if( ( ! $post = get_post( $post_id ) ) || ( ! $post = get_post( $post ) ) )
		return;		
	 
	$metas = ( isset( $_POST['mkpbx_postbox']['single'] ) ) ? $_POST['mkpbx_postbox']['single'] : null;
	
	$metas = apply_filters( 'mkpbx_sanitize_single_metadata', $metas, $post );
		
	foreach( (array) $metas as $metakey => $metavalue ) :
		if ( empty( $metavalue ) ):
			delete_post_meta( $post->ID, '_'.$metakey );
		elseif ( $metavalue != get_post_meta( $post->ID, '_'.$metakey, true ) ):
			update_post_meta( $post->ID, '_'.$metakey, $metavalue );		
		endif;		
	endforeach;

	return $post;
}
add_action( 'save_post', 'mkpbx_save_post_single_metadata', null, 2 );

/**
 *  Enregistrement des métadonnées de post en mode multi
 */
function mkpbx_save_post_multi_metadata( $post_id, $post ){
	// Contrôle s'il s'agit d'une routine de sauvegarde automatique.	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;
	// Contrôle s'il s'agit d'une routine de sauvegarde automatique.	
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
      return;	
	
	//Bypass
	if( !isset( $_POST['post_type'] ) )
		return;
		
	// Contrôle des permissions
  	if ( 'page' == $_POST['post_type'] )
    	if ( !current_user_can( 'edit_page', $post_id ) )
        	return;
  	else
    	if ( !current_user_can( 'edit_post', $post_id ) )
        	return;
			
	if( ( ! $post = get_post( $post_id ) ) || ( ! $post = get_post( $post ) ) )
		return;			
	
	$metas = ( isset( $_POST['mkpbx_postbox']['multi'] ) ) ? $_POST['mkpbx_postbox']['multi'] : null;
	$metas = apply_filters( 'mkpbx_sanitize_multi_metadata', $metas, $post );

	$exists = has_meta( $post->ID );

	foreach( $exists as $exist ) :		
		if ( ! isset( $metas[substr($exist['meta_key'], 1)] ) )
			continue;
		if ( ! isset( $metas[substr($exist['meta_key'], 1) ][$exist['meta_id']] ) )
			delete_metadata_by_mid( 'post', $exist['meta_id'] );		
	endforeach;

	if ( isset( $metas ) )		
		foreach( $metas as $meta_key => $meta_ids )
			foreach( $meta_ids as $meta_id => $meta_value )
				if( !$meta_value )
					continue;
				elseif( is_int( $meta_id ) && get_post_meta_by_id( $meta_id ) )
					update_metadata_by_mid( 'post', $meta_id, $meta_value );
				else
					add_post_meta($post_id, '_'.$meta_key, $meta_value );
	
	return $post;
}
add_action( 'save_post', 'mkpbx_save_post_multi_metadata', null, 2 );

/**
 *  Enregistrement des options de post
 */
function mkpbx_save_post_option( $post_id, $post ){
	// Contrôle s'il s'agit d'une routine de sauvegarde automatique.	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;
	// Contrôle s'il s'agit d'une routine de sauvegarde automatique.	
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
      return;	

	//Bypass
	if( !isset($_POST['post_type']) )
		return;
		
	// Contrôle des permissions
  	if ( 'page' == $_POST['post_type'] )
    	if ( !current_user_can( 'edit_page', $post_id ) )
        	return;
  	else
    	if ( !current_user_can( 'edit_post', $post_id ) )
        	return;
			
	if( ( ! $post = get_post( $post_id ) ) || ( ! $post = get_post( $post ) ) )
		return;		
	 
	if( ! ( $options = ( isset( $_POST['mkpbx_postbox']['option'] ) ) ? $_POST['mkpbx_postbox']['option'] : null ) )
		return; 
	
	$options = apply_filters( 'mkpbx_sanitize_option', $options, $post );
	
	foreach( $options as $name => $value )
		update_option( $name, $value );

	return $post;
}
add_action( 'save_post', 'mkpbx_save_post_option', null, 2 );
