<?php
class tiFy_ajax_actions{
	public	$tiFy;
	/**
	 * Initialisation
	 */
	function __construct(tiFY $tiFy ){
		$this->tiFy = $tiFy;
		
		// Action Ajax
		add_action( 'wp_ajax_tify_get_post_permalink', array( $this, 'get_post_permalink' ) );
		add_action( 'wp_ajax_tify_autocomplete', array( $this, 'autocomplete' ) );
	}
	
	/**
	 * Récupération d'un permalien de post selon son ID 
	 */
	function get_post_permalink(){
		$post_id = isset( $_POST['post_id'] )? intval( $_POST['post_id'] ) : 0;
		
		$permalink = get_permalink( $post_id );
		// Affichage du lien en relatif 
		if( ! empty( $_POST['relative'] ) ) 
			$permalink = preg_replace( '/'. preg_quote( site_url(), '/' ) .'/', '', $permalink );
		wp_die( $permalink );
	}
	
	/**
	 * Récupération de contenus pour l'autocompletion
	 */
	function autocomplete(){
		$return = array();
	
		// Vérification du type de requête
		if ( isset( $_REQUEST['autocomplete_type'] ) )
			$type = $_REQUEST['autocomplete_type'];
		else
			$type = 'add';
		
		if ( isset( $_REQUEST['post_type'] ) )
			$post_type = explode( ',', $_REQUEST['post_type'] );
		else
			$post_type = 'any';
	
		$query_post = new WP_Query;
		
		$posts = $query_post->query( array(
			'post_type' => $post_type,
			's' => $_REQUEST['term'],
			'posts_per_page' => -1
		));
		foreach ( $posts as $post ) {
			$post->label = sprintf( __( '%1$s' ), $post->post_title ); 
			$post->value = $post->post_title;
			$post->ico = ( $ico = wp_get_attachment_image( get_post_thumbnail_id( $post->ID ), array(50,50), false, array( 'style' => 'float:left; margin-right:5px;' ) ) )? $ico :'<div style="background-color:#CCC; width:50px; height:50px; float:left; margin-right:5px;"></div>';
			$post->thumb = ( $thumb = wp_get_attachment_image( get_post_thumbnail_id( $post->ID ), 'thumbnail', false, array( 'style' => '' ) ) )? $thumb :'<div style="background-color:#CCC; width:150px; height:150px;"></div>';
			$post->type = get_post_type_object( $post->post_type )->label;
			$post->id = $post->ID;	
			$return[] = $post;
		}
		wp_die( json_encode( $return ) );	
	}
}