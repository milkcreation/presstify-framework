<?php
class tiFy_Taboox_RelatedPosts{
	/**
	 * Initialisation
	 */
	function __construct(){
		add_action( 'admin_init', array( $this, 'admin_init' ), 11 );
		add_action( 'wp_ajax_mkpbx_related_posts_autocomplete', array( $this, 'wp_ajax_autocomplete' ) );
	}
	
	/**
	 * Initialisation de l'interface d'administration
	 */
	function admin_init(){
		// Initialisation des scripts
		wp_localize_script( 'taboox-related_posts', 'tify_taboox_related_posts', array(
				'maxAttempt' => __( 'Nombre maximum de contenu en relation atteint', 'tify' ),
			)
		);
	}	
	
	/**
	 * 
	 */
	function admin_render( $post = null, $args = array() ){
		// Bypass	
		if( ! $post = get_post( $post) )
			return;
		
		global $mkpbx_related_posts_order;
		$mkpbx_related_posts_order = 0;
		$args = wp_parse_args( $args, array(
				'name' 			=> 'related_posts',
				'post_type' 	=> 'any',
				'post_status' 	=> 'publish',
				'placeholder'	=> __( 'Rechercher un contenu en relation', 'tify' ),
				'max'			=> -1
			)
		);
		extract( $args );		
	?>	
		<div id="mkpbx_post_related">
			<input type="hidden" name="mkpbx_postbox[single][<?php echo $name;?>][]" value="" />
			<input 	type="text" 
					id="mkpbx_related_posts-search" 
					class="mkpbx_related_posts-search widefat" 
					data-post_type="<?php echo $post_type;?>" 
					data-post_status="<?php echo $post_status;?>"
					data-max="<?php echo $max;?>"  
					data-name="<?php echo $name;?>" 
					placeholder="<?php echo $placeholder;?>" 
					autocomplete="off" />
			<ul id="mkpbx_related_posts-list" class="mkpbx_related_posts-list thumb-list">
			<?php 	
				if( $related_posts = get_post_meta( $post->ID, '_'.$name, true ) )
					foreach( $related_posts as $post_id ) 
						if ( ! $post_id ) 
							continue;
						else 
							echo $this->admin_item_render( $post_id, $name, ++$mkpbx_related_posts_order );
			?>	
			</ul>	
		</div>			
	<?php
	}

	/**
	 * Rendu d'un élément
	 */
	function admin_item_render( $post_id, $name, $order ){
		// Bypass	
		if( ! $post = get_post( $post_id ) )
			return;
		
		$output  = "";
		$output .= "\n<li>";
		$output .= ( $thumb = wp_get_attachment_image( get_post_thumbnail_id( $post_id ), 'thumbnail', false, array( 'style' => '' ) ) )? $thumb :'<div style="background-color:#CCC; width:150px; height:150px;"></div>';
		$output .= "\n\t<h4 class=\"post_title\">".get_the_title( $post_id )."</h4>";
		$output .= "\n\t<em class=\"post_type\">".get_post_type_object( get_post_type( $post_id ) )->label."</em>";
		$output .= "\n\t<em class=\"post_status\">". get_post_status_object( get_post_status( $post_id ) )->label ."</em>";
		$output .= "\n\t<input class=\"post_id\" type=\"hidden\" name=\"mkpbx_postbox[single][".$name."][]\" value=\"$post_id\" />";
		$output .= "\n\t<a href=\"#remove\" class=\"tify_button_remove remove\"></a>";
		$output .= "\n\t<input type=\"text\" class=\"order\" value=\"$order\" size=\"1\" readonly autocomplete=\"off\"/>";	
		$output .= "\n</li>";
		
		return apply_filters( 'mkpbx_related_posts_item', $output, $post_id, $name, $order );
	}

	/**
	 * Récupération Ajax de contenus pour l'autocompletion
	 */
	function wp_ajax_autocomplete(){
		$return = array();
		
		// Vérification du type de requête
		if ( isset( $_REQUEST['autocomplete_type'] ) )
			$type = $_REQUEST['autocomplete_type'];
		else
			$type = 'add';
		
		if ( ! empty( $_REQUEST['post_type'] ) )
			$post_type = explode( ',', $_REQUEST['post_type'] );
		else
			$post_type = 'any';
		
		if ( ! empty( $_REQUEST['post_status'] ) )
			$post_status = explode( ',', $_REQUEST['post_status'] );
		else
			$post_status = 'publish';
		
		$query_args = array(
			'post_type' => $post_type,
			'post_status' => $post_status,
			's' => $_REQUEST['term'],
			'posts_per_page' => -1
		);
		
		if( isset( $_REQUEST['post__not_in'] ) )	
			$query_args['post__not_in'] = $_REQUEST['post__not_in'];

		$query_post = new WP_Query;
		$posts = $query_post->query( $query_args );
		foreach ( $posts as $post ) {
			$post->label 		= sprintf( __( '%1$s' ), $post->post_title ); 
			$post->value 		= $post->post_title;
			$post->ico 			= ( $ico = wp_get_attachment_image( get_post_thumbnail_id( $post->ID ), array(50,50), false, array( 'style' => 'float:left; margin-right:5px;' ) ) )? $ico :'<div style="background-color:#CCC; width:50px; height:50px; float:left; margin-right:5px;"></div>';
			$post->thumb 		= ( $thumb = wp_get_attachment_image( get_post_thumbnail_id( $post->ID ), 'thumbnail', false, array( 'style' => '' ) ) )? $thumb :'<div style="background-color:#CCC; width:150px; height:150px;"></div>';
			$post->type 		= get_post_type_object( $post->post_type )->label;
			$post->id 			= $post->ID;
			$post->render 		= apply_filters( 
									'mktzr_ajax_autocomplete_render', 
									"<a style=\"display:block; min-height:50px;\">"
									. $post->ico ." ". $post->label ."<br>"
									."<em style=\"font-size:0.8em;\"><strong>{$post->type}</strong></em>"
									."<em class=\"post_status\" style=\"position:absolute; right:10px; top:50%; line-height:1; margin-top:-8px; font-size:12px; font-weight:600\">". get_post_status_object( get_post_status( $post->ID ) )->label ."</em>"
									."</a>",
									$post
								  );	
			$post->display 		= $this->admin_item_render( $post->ID, $_REQUEST['name'], $_REQUEST['order'] );	
			$return[] = $post;
		}
		wp_die( json_encode( $return ) );	
	}	
}
global $tify_taboox_related_posts;
$tify_taboox_related_posts = new tiFy_Taboox_RelatedPosts;

/**
 * Metaboxe de saisie de la galerie d'image
 */ 
function mkpbx_related_posts_render( $post, $args = array() ){
	global $tify_taboox_related_posts;
	
	return $tify_taboox_related_posts->admin_render( $post, $args );
}

/**
 * Vérifie l'existance de contenu en relation
 */
function mkpbx_has_related_posts( $post_id = null, $args = array() ){
	if( mkpbx_get_related_posts( $post_id, $args ) )
		return true;
}

/**
 * Récupération des contenus en relation
 */
function mkpbx_get_related_posts( $post_id = null, $args = array() ){
	$args = wp_parse_args( $args, array(
			'name' 		=> 'related_posts',
			'post_type' => 'any' 
		)
	);
	extract( $args );
	
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	
	if( $related_posts = get_post_meta( $post_id, '_'.$name, true ) ) :
		foreach( $related_posts as $k => $related_post ) 
			if( !$related_post ) $related_posts = array_slice( $related_posts, 1 );
	endif;
			
	return $related_posts;
}
 
/**
 * Affichage des contenus en relation
 */
function mkpbx_related_posts_display( $post_id = null, $args = array() ){
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;

	$defaults = array( 
		'max'	=> -1,
		'echo' 	=> true 
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$output  = "";
	
	if( $related_posts = mkpbx_get_related_posts( $post_id, $args ) ) :
		$related_query = new WP_Query( array( 'post_type' => 'any', 'post__in' => $related_posts, 'posts_per_page' => $max, 'orderby' => 'post__in' ) );

		if( $related_query->have_posts() ) :
			$output .= "\n<div class=\"mkpbx_related_posts\">";	
			$output .= "\n\t<ul class=\"roller\">";
			while( $related_query->have_posts() ): $related_query->the_post();
				$item  = "\n\t\t<li>";
				$item .= "\n\t\t<a href=\"".get_permalink()."\">";
				$item .= get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
				$item .= "\n\t\t\t<h3>".get_the_title( get_the_ID() )."</h3>";
				$item .= "\n\t\t</a>";
				$item .= "\n\t\t</li>";
				$output .= apply_filters( 'mkpbx_related_posts_display_item', $item, get_the_ID() );
			endwhile; ;
			$output .= "\n\t</ul>";
			$output .= "\n</div>";
		endif; wp_reset_postdata();
	endif;
	
	if( $echo )
		echo $output;
	else
		return $output;		
}