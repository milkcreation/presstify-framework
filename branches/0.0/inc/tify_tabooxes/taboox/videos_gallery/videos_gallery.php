<?php
/**
 * ASSOCIATION DE VIDEOS
 */

/**
 * Metaboxe de saisie
 */ 
function mkpbx_videos_gallery_render( $post, $args = array() ){
	// Bypass	
	if( ! $post = get_post( $post) )
		return;
	
	$args = wp_parse_args( $args, array(
			'name' => 'videos_gallery'
		)
	);
	extract( $args );
	
	$metadatas = has_meta( $post->ID );
	
	foreach ( $metadatas as $key => $value )
		if ( $metadatas[ $key ][ 'meta_key' ] != '_'.$name )
			unset( $metadatas[ $key ] );
		else
			$metadatas[ $key ]['meta_value'] = wp_parse_args( maybe_unserialize( $metadatas[ $key ]['meta_value'] ), array( 'src' => '', 'poster' => '' ) );
?>	
	<div class="videos_gallery-postbox">
		<input type="hidden" name="mkpbx_postbox[videos_gallery][names][]" value="<?php echo $name;?>" />
		<ul id="videos_gallery-<?php echo sanitize_title($name);?>-list" class="videos_gallery-list">
		<?php foreach( $metadatas as $meta ) mkpbx_videos_gallery_item_render( $meta, $name );?>
		</ul>
		<a href="#" 
			class="videos_gallery-add button-secondary" 
			data-name="<?php echo $name;?>" 
		>
			<span class="dashicons dashicons-video-alt2" style="vertical-align:middle;"></span>&nbsp;&nbsp;<?php _e( 'Ajouter une vidéo', 'tify' );?>
		</a>
		<span class="spinner"></span> 			
	</div>				
<?php	
}

/**
 * Rendu de l'interface de saisie d'une vidéo
 */
function mkpbx_videos_gallery_item_render( $meta = null, $name ){
	$defaults = array(
		'meta_key' 		=> '_'.$name,
		'meta_value' 	=> '',
		'meta_id'		=> uniqid()
	);
	$meta 	= wp_parse_args( $meta, $defaults );
	$attr 	= wp_parse_args( $meta['meta_value'], array( 'src' => '', 'poster' => '' ) );
?>
<li>
	
	<div class="poster"> 
		<a href="#mkpbx-videos_gallery_poster-add"
			class="videos_gallery-poster-add"
			data-media_title="<?php _e( 'Sélectionner une jaquette', 'mktzr_postbox' );?>"
			data-media_button_text="<?php _e( 'Ajouter la jaquette', 'mktzr_postbox' );?>"
			<?php echo ( $bkg = ( $attr['poster'] && ( $image = wp_get_attachment_image_src( $attr['poster'], 'thumbnail' ) ) ) )? "style=\"background-image:url($image[0]);\"" : "";?>
		>
			<?php _e( 'Changer la jaquette', 'mkpbx' );?>
			<input type="hidden" name="mkpbx_postbox[multi][<?php echo $name;?>][<?php echo $meta['meta_id'];?>][poster]" value="<?php echo $attr['poster'];?>" />
		</a>
	</div>
	<div class="src">
		<textarea 
			name="mkpbx_postbox[multi][<?php echo $name;?>][<?php echo $meta['meta_id'];?>][src]" rows="5" cols="40"
			placeholder="<?php _e( 'Vidéo de la galerie ou iframe', 'mktzr_postbox' );?>" 
			class="videos_gallery-src"><?php echo $attr['src'];?></textarea>
		<a href="#" 
			class="dashicons dashicons-admin-media videos_gallery-media-add" 
			data-media_title="<?php _e( 'Sélectionner une vidéo', 'mktzr_postbox' );?>"
			data-media_button_text="<?php _e( 'Ajouter la vidéo', 'mktzr_postbox' );?>"
		></a>
	</div>
	<a href="#" class="videos_gallery-remove dashicons dashicons-no"></a>					
</li>
<?php
}

/**
 * Ajout d'une interface de saisie vidéo via Ajax
 */
function mkpbx_videos_gallery_ajax_add(){
	mkpbx_videos_gallery_item_render( null, $_POST['name'] );
	exit;
}
add_action( 'wp_ajax_mkpbx_videos_gallery_add', 'mkpbx_videos_gallery_ajax_add' );

/**
 * Sauvegarde
 */
function mkpbx_videos_gallery_sanitize_multi_metadata( $metas, $post ){
	if( ! isset( $_REQUEST['mkpbx_postbox']['videos_gallery']['names'] ) )
		return $metas;

	$exists = has_meta( $post->ID );
	
	foreach( $_REQUEST['mkpbx_postbox']['videos_gallery']['names'] as $name ) :
		foreach( $exists as $exist ) :
			if( $exist['meta_key'] != '_'.$name )
				continue;
			if( ! isset( $metas[$name][$exist['meta_id']] ) )
				delete_metadata_by_mid( 'post', $exist['meta_id'] );
		endforeach;
	endforeach;
	
	return $metas;	
}
add_filter( 'mkpbx_sanitize_multi_metadata', 'mkpbx_videos_gallery_sanitize_multi_metadata', null, 2 );