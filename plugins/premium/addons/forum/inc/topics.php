<?php
Class tiFy_Forum_Topics{
	public	// Contrôleurs
			$master;
			
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Forum $master ){
		$this->master = $master;
		
		// Actions et filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ) );
		add_action( 'add_meta_boxes', array( $this, 'wp_add_meta_boxes' ), null, 2 ); 
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** Initialisation globale == **/
	function wp_init(){
		// Déclaration du type Sujet
		register_post_type( 'tify_forum_topic', 
			array(
				'labels' => array(
					'name'			 	=> __( 'Sujets', 'tify' ),				
					'singular_name' 	=> __( 'Sujet', 'tify' ),
					'menu_name'			=> __( 'Sujet de forum', 'tify' ),
					'name_admin_bar' 	=> __( 'Sujets de forum', 'add new on admin bar', 'tify' ),
					'add_new'			=> __( 'Ajouter un sujet', 'tify' ),
					'all_items' 		=> __( 'Tous les sujets', 'tify' ),
					'add_new_item'		=> __( 'Ajouter un sujet', 'tify' ),
					'edit_item'			=> __( 'Éditer le sujet', 'tify' ),
					'new_item'			=> __( 'Nouveau sujet', 'tify' ),
				 	'view_item'			=> __( 'Afficher le sujet', 'tify' ),
					'search_items'		=> __( 'Rechercher un sujet', 'tify' ),
					'not_found'			=> __( 'Aucun sujet trouvé', 'tify' ),		
					'not_found_in_trash'=> __( 'Aucun sujet dans la corbeille', 'tify' )				
				),
				'description'			=> __( 'Sujets de Forum PressTiFy', 'tify' ),
				'public'				=> true,
				'exclude_from_search'	=> true,
				'publicly_queryable' 	=> true,
		    	'show_ui' 				=> true,
		    	'show_in_nav_menus' 	=> true,
		    	'show_in_menu' 			=> false,		
				'show_in_admin_bar' 	=> true,
				'menu_position'			=> null,
		    	'capability_type' 		=> 'page',
				'map_meta_cap' 			=> true,
				'hierarchical' 			=> false,
				'supports' 				=> array( 'title', 'editor', 'page-attributes', 'comments' ),
		    	'has_archive' 			=> false,
		    	'permalink_epmask'		=> EP_PERMALINK,
		    	'rewrite' 				=> array( 'slug'=> ( $this->master->hook_page_id() ? get_post( $this->master->hook_page_id() )->post_name : 'tify_forum_topic' ), 'with_front'=> false ),			
			   	'query_var' 			=> true,
		    	'can_export'			=> true		
			)
		);
	}

	/** == Menu d'administration == **/
	function wp_admin_menu(){
		add_submenu_page( $this->master->menu_slug, __( 'Tous les sujets', 'tify' ), __( 'Tous les sujets', 'tify' ), 'edit_posts', 'edit.php?post_type=tify_forum_topic' );
		add_submenu_page( $this->master->menu_slug, __( 'Ajouter un sujet de forum', 'tify' ), __( 'Ajouter un sujet', 'tify' ), 'edit_posts', 'post-new.php?post_type=tify_forum_topic' );	
	}
		
	/** == Déclaration des metaboxes == **/
	function wp_add_meta_boxes( $post_type, $post = array() ){
		// Bypass	
		if( $post_type != 'tify_forum_topic' )
			return;
		
		remove_meta_box( 'commentstatusdiv', $post_type, 'normal' ); 
		
		tify_controls_enqueue( 'switch' );
		add_meta_box( 'tify_forum_topic_contrib_open', __( 'Permettre les contributions', 'tify' ), array( $this, 'meta_box_topic_contrib_open' ), $post_type, 'side', 'default' ); 	
		
		if( post_type_exists( 'tify_forum' ) )
			add_meta_box( 'tify_forum_topic_related_forum', __( 'Forum associé', 'tify' ), array( $this, 'meta_box_topic_related_forum' ), $post_type, 'side', 'default' );	
	}
	
	/* = METABOXES = */
	/** == Permettre les contributions == **/
	function meta_box_topic_contrib_open( $post ){
		//<input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($post->comment_status, 'open');
		tify_control_switch(
			array(
				'id'		=> 'comment_status',
				'name' 		=> 'comment_status',
				'value_on'	=> 'open',
				'value_off'	=> 'closed',
				'checked'	=> $post->comment_status  
			)
		);
	}
	
	/** == Forum associé == **/
	function meta_box_topic_related_forum( $post ){
		// Bypass
		if( ! $post = get_post( $post) )
			return;
			
		$args['name'] 				= 'mkpbx_postbox[single][topic_related_forum]';
		$args['selected'] 			= get_post_meta( $post->ID, '_topic_related_forum', true );
		$args['show_option_none'] 	= __( 'Aucun Forum associé', 'tify');
		$args['post_type']			= 'tify_forum';
		
		wp_dropdown_pages( $args );
	}
}