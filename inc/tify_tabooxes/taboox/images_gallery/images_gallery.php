<?php
/**
 * GALERIE D'IMAGES
 */
/**
 * Déclaration de la taboox
 */
add_action( 'tify_taboox_register_form', 'tify_taboox_images_gallery_init' );
function tify_taboox_images_gallery_init(){
	tify_taboox_register_form( 'tify_taboox_images_gallery' );
}

/**
 * Taboox de réglage de la galerie d'image
 */
class tify_taboox_images_gallery extends tify_taboox{
	public 	$name = 'gallery_images';
	
	/**
	 * Initialisation de la classe
	 */
	function __construct( ){	
		parent::__construct(
			// Options
			array(
				'dir'				=> dirname( __FILE__ ),
				'instances'  		=> -1
			)
		);		
	}
	
	/**
	 * Déclaration des scripts
	 */
	function register_scripts(){
		wp_register_style( 'tify_taboox_images_gallery', $this->uri .'/admin.css', array( ), '150325' );
		wp_register_script( 'tify_taboox_images_gallery', $this->uri .'/admin.js', array( 'jquery' ), '150325', true );
	}
	
	/**
	 * Mise en file des scripts
	 */
	function enqueue_scripts(){
		wp_enqueue_media();
		
		wp_enqueue_script( 'tify_taboox_images_gallery' );
		wp_enqueue_style( 'tify_taboox_images_gallery' );
	}
	
	/**
	 * Formulaire de saisie
	 */
	function form( $_args = array() ){
		$this->_parse_args( $_args );
	?>
	<div id="taboox_images_gallery-<?php echo $this->instance;?>" class="images_gallery-postbox">
		<input type="hidden" name="<?php echo $this->name;?>[]" value="0" />
		<ul id="images-gallery-<?php echo $this->name;?>-list" class="images-gallery-list thumb-list">			
		<?php 	
			if( ! empty( $this->value ) )
				foreach( $this->value as $attachment_id )
					echo mkpbx_images_gallery_item( $attachment_id, $this->args );
		?>	
		</ul>
		<a href="#" class="images_gallery-add button-secondary" 
			data-name="gallery_images" 
			data-media_title="<?php _e( 'Galerie d\'image', 'tify' );?>"
			data-media_button_text="<?php _e( 'Ajouter les images', 'tify' );?>">
			<div class="dashicons dashicons-format-gallery" style="vertical-align:middle;"></div>&nbsp;
			<?php _e( 'Ajouter des images', 'tify' );?>
		</a>
	</div>					
	<?php
	}
}


/**
 * Rendu d'un élément de la galerie d'images
 */
function mkpbx_images_gallery_item( $attachment_id, $args ){
	// Bypass
	if( ! $image = wp_get_attachment_image_src( $attachment_id, 'thumbnail' ) )
		return;
		
	static $mkpbx_images_gallery_order = 0;	
	$mkpbx_images_gallery_order++;
	
	$args = wp_parse_args( $args, array(
			'name' => 'gallery_images'
		)
	);
	extract( $args );
	
	$output  = "";
	$output .= "\n<li>";
	$output .= "\n\t<img width=\"{$image[1]}\" height=\"{$image[2]}\" src=\"{$image[0]}\" class=\"attachment-thumbnail\" />";
	$output .= "\n\t<input type=\"hidden\" name=\"mkpbx_postbox[single][". $name ."][]\" value=\"$attachment_id\" />";
	$output .= "\n\t<a href=\"#remove\" class=\"tify_button_remove\"></a>";	
	$output .= "\n\t<input type=\"text\" class=\"order\" value=\"$mkpbx_images_gallery_order\" size=\"1\" readonly />";	
	$output .= "\n</li>";
	
	return $output;
}

/**
 * Récupération des images de la galerie
 */
function mkpbx_get_images_gallery( $post_id = null, $args = array() ){
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	
	$args = wp_parse_args( $args, array(
			'name' => 'gallery_images'
		)
	);
	extract( $args );
	
	if( $gallery_images = get_post_meta( $post_id, '_'.$name, true ) ) :
		foreach( $gallery_images as $k => $gallery_image ) 
			if( ! $gallery_image ) $gallery_images = array_slice( $gallery_images, 1 );
	endif;
			
	return $gallery_images;
}

/**
 * Vérification d'image dans la galerie
 */
function has_images_gallery( $post_id = null, $args = array() ){
	if( mkpbx_get_images_gallery( $post_id, $args ) )
		return true;
}

/**
 * Affichage de la galerie d'images
 */
function the_images_gallery( $post_id = null, $args = array() ){
	echo get_the_images_gallery( $post_id, $args );
}

/**
 * Récupération de la galerie d'images
 */
function get_the_images_gallery( $post_id = null, $args = array() ){
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	
	$args = wp_parse_args( $args, array(
			'name' => 'gallery_images'
		)
	);
	extract( $args );
	// Bypass
	if( ! has_images_gallery( $post_id, $args ) )
		return;
	
	static $instances = 0;
	$instances++;
	
	if( ! $images = mkpbx_get_images_gallery( $post_id, $args ) )
		return;

	$output  = "";
	$output .= "\n<div id=\"mkpbx_images_gallery-$post_id-$instances\" class=\"mkpbx_images_gallery\">";
	$output .= "\n\t<div class=\"viewer\">";
	$output .= "\n\t\t<ul class=\"roller\">";
	foreach( $images as $img_id )
		if( ! empty( $img_id ) && ( $src = wp_get_attachment_image_src( $img_id, 'full' ) ) )
			$output .= "\n\t\t\t<li><div class=\"item-image\" style=\"background-image:url({$src[0]});\"></div></li>";
	$output .= "\n\t\t</ul>";
	$output .= "\n\t</div>";
	$output .= "\n\t<a href=\"#mkpbx_images_gallery-$post_id-$instances\" class=\"nav prev\"></a>";
	$output .= "\n\t<a href=\"#mkpbx_images_gallery-$post_id-$instances\" class=\"nav next\"></a>";
	$output .= "\n\t\t<ul class=\"tabs\">";
	$n = 1;
	reset( $images );
	foreach( $images as $img_id )
		if( ! empty( $img_id ) && ( $src = wp_get_attachment_image_src( $img_id, 'full' ) ) )
			$output .= "\n\t\t<li><a href=\"#mkpbx_images_gallery-$post_id-$instances\">".( $n++ )."</a></li>";	
	$output .= "\n\t\t</ul>"; 
	$output .= "\n</div>";	

	return apply_filters( 'mkpbx_images_gallery_display', $output, $images, $instances, $post_id );
}

/**
 * DEPRECATED 
 */ 
/**
 * Metaboxe de saisie de la galerie d'image
 */ 
function mkpbx_images_gallery_render( $post = null, $args = array() ){
	global $tiFy_tabooxes_master;
	
	$tiFy_tabooxes_master->tabooxes['tify_taboox_images_gallery']->form( array( $post, $args, 'post' ) );			
}