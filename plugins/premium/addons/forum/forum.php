<?php
/*
Addon Name: Forum
Addon URI: http://presstify.com/theme-manager/addons/premium/forum
Description: Gestion de forum
Version: 0.150423
Author: Jordy Manner
Author URI: http://profile.milkcreation.fr/jordy
*/

Class tiFy_Forum{
	public 	// Chemins
			$dir,
			$uri,
			
			// Configuration
			$page_slug,
			$is_multi,
			$form_id,
			$roles,
			
			// Paramètres
			$menu_slug,
			
			// Contrôleurs
			$topics,
			$contribs,
			$contributors,
			$template,
			$options,
			
			// Librairie
			$tify_forms;
					
	/* = CONSTRUCTEUR = */
	function __construct(){
		// Définition des chemins	
		$this->dir 	= dirname(__FILE__);
		$this->uri	= plugin_dir_url(__FILE__);
		
		// Configuration
		$this->page_slug 	= 'tify_forum';
		$this->is_multi 	= false;
		$this->form_id		= 9999;
		$this->roles 		= array( 
			'tify_forum_subscriber' => array(
				'name' 					=> __( 'Contributeur de forum', 'tify' ),
				'capabilities'			=> array(),
				'show_admin_bar_front' 	=> false
			)
		);
		
		// Paramètres
		$this->menu_slug 	= $this->is_multi ? "edit.php?post_type=tify_forum" : "edit.php?post_type=tify_forum_topic";
					
		// Initialisation des contrôleurs
		/// Sujet de forum
		require_once $this->dir .'/inc/topics.php';
		$this->topics = new tiFy_Forum_Topics( $this );
		/// Contributeurs
		require_once $this->dir .'/inc/contributors.php';	
		$this->contributors = new tiFy_Forum_Contributors( $this );	
		/// Contributions
		require_once $this->dir .'/inc/contribs.php';
		$this->contribs = new tiFy_Forum_Contribs( $this );	
			
		require_once $this->dir .'/inc/general-template.php';
		$this->template = new tiFy_Forum_Template( $this );		
		require_once $this->dir .'/inc/options.php';
		$this->options = new tiFy_Forum_Options( $this );
		
		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ), 9 );
				
		// Actions et Filtres PressTiFy
		global $tiFy;
		/// tiFy_forms
		require_once(  $tiFy->dir .'/plugins/forms/forms.php' );
		global $tify_forms;
		$this->tify_forms = $tify_forms;
	}

	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** Initialisation globale de Worpdress == **/
	function wp_init(){
		// Déclaration du type Forum
		if( $this->is_multi )
			register_post_type( 'tify_forum', array(
					'labels' => array(
						'name'			 	=> __( 'Forums', 'tify' ),
						'singular_name' 	=> __( 'Forum', 'tify' ),			
						'add_new'			=> __( 'Ajouter un forum', 'tify' ),
						'all_items' 		=> __( 'Tous les forums', 'tify' ),
						'add_new_item'		=> __( 'Ajouter un nouveau forum', 'tify' ),
						'edit_item'			=> __( 'Éditer le forum', 'tify' ),
						'new_item'			=> __( 'Nouveau forum', 'tify' ),
					 	'view_item'			=> __( 'Afficher le forum', 'tify' ),
						'search_items'		=> __( 'Rechercher un forum', 'tify' ),
						'not_found'			=> __( 'Aucun forum trouvé', 'tify' ),		
						'not_found_in_trash'=> __( 'Aucun forum dans la corbeille', 'tify' ),
						'parent_item_colon'	=> __( 'Forum parent', 'tify' ),
						'menu_name' 		=> __( 'Forums', 'add new on admin bar', 'tify' ),				
					),
					'description'			=> __( 'Forum PressTiFy basé sur les commentaires', 'tify' ),
					'public'				=> true,
					'exclude_from_search'	=> false,
					'publicly_queryable' 	=> true,
			    	'show_ui' 				=> true,
			    	'show_in_nav_menus' 	=> true,
			    	'show_in_menu' 			=> false,		
					'show_in_admin_bar' 	=> false,
					'menu_position'			=> null,
					'menu_icon'				=> false,
					'capability_type' 		=> 'page',
					'map_meta_cap' 			=> true,
					'hierarchical' 			=> true,
					'supports' 				=> array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			    	'has_archive' 			=> true,
			    	'permalink_epmask'		=> EP_PERMALINK,
			    	'rewrite' 				=> array( 'slug'=> __( 'forum', 'tify' ), 'with_front'=> false ),			
				   	'query_var' 			=> true,
			    	'can_export'			=> true,		
				)
			);
		
		/**
		register_taxonomy( 'mktopics-cats', array( 'mocca-topics' ), array(
				'labels' => array(
					'name'			=> __( 'Topic\'s categories', 'milk-forums' ), 
					'singular_name' => __( 'Topic\'s category', 'milk-forums' ),
					'search_items' 	=> __( 'Search topic\'s category', 'milk-forums' ),
					'popular_items' => __( 'Most popular topic\'s categories', 'milk-forums' ),
					'all_items' 	=> __( 'All topic\'s categories', 'milk-forums' ),
					'parent_item' => __( 'Parent topic\'s category', 'milk-forums' ),
					'parent_item_colon' => null,
					'edit_item' 	=> __( 'Edit topic\'s category', 'milk-forums' ),
					'update_item' => __( 'Update topic\'s category' ),
					'add_new_item' 	=> __( 'Add topic\'s category', 'milk-forums' ),				
					'new_item_name' 		=> __( 'New topic\'s category', 'milk-forums' ),
					'separate_items_with_commas' => null,
					'add_or_remove_items' => null,
					'choose_from_most_used' => null,
					'menu_name' => null				
				),
				'public' => true,
				'show_in_nav_menus'=>false,
				'show_ui' =>true,
				'show_tagcloud' => false,
				'hierarchical' => true,		
				'update_count_callback' => '',
				'rewrite' => array( 'slug'=>__( 'topic-category', 'milk-forums' ), 'with_front'=> false )	
			)
		);
			
		register_taxonomy( 'mktopics-tags', array( 'mocca-topics' ), array(
				'labels' => array(
					'name'			=> __( 'Topic\'s tags', 'milk-forums' ), 
					'singular_name' => __( 'Topic\'s tag', 'milk-forums' ),
					'search_items' 	=> __( 'Search topic\'s tag', 'milk-forums' ),
					'popular_items' => __( 'Most popular topic\'s tags', 'milk-forums' ),
					'all_items' 	=> __( 'All topic\'s tags', 'milk-forums' ),
					'parent_item' => __( 'Parent topic\'s tag', 'milk-forums' ),
					'parent_item_colon' => null,
					'edit_item' 	=> __( 'Edit topic\'s tag', 'milk-forums' ),
					'update_item' => __( 'Update topic\'s tag' ),
					'add_new_item' 	=> __( 'Add topic\'s tag', 'milk-forums' ),				
					'new_item_name' 		=> __( 'New topic\'s tag', 'milk-forums' ),
					'separate_items_with_commas' => null,
					'add_or_remove_items' => null,
					'choose_from_most_used' => null,
					'menu_name' => null				
				),
				'public' => true,
				'show_in_nav_menus'=>false,
				'show_ui' => true,
				'show_tagcloud' => true,
				'hierarchical' => false,		
				'update_count_callback' => '',
				'rewrite' => array( 'slug'=>__( 'topic-tag', 'milk-forums' ), 'with_front'=> false )	
			)
		);	**/	
		
	}
	/** == Entrée de menu de l'interface d'administration == **/
	function wp_admin_menu(){
		// Menu d'administration		
		add_menu_page( 'Forums', __( 'Forums', 'tify' ), 'edit_posts', $this->menu_slug, null, 'dashicons-megaphone' );
		// Forum
		if( $this->is_multi ) :
			add_submenu_page( $this->menu_slug, __( 'Tous les forums', 'tify' ), __( 'Tous les forums', 'tify' ), 'edit_posts', $this->menu_slug );
			add_submenu_page( $this->menu_slug, __( 'Ajouter un forum', 'tify' ), __( 'Ajouter un forum', 'tify' ), 'edit_posts', 'post-new.php?post_type=mkforums' );
		endif;
		// Taxonomies		
		/// add_submenu_page( $menu_slug, __( 'Topic\'s category | %s', 'tify' ), $title ), __( 'Add topic category', 'tify' ), 'edit_posts', 'edit-tags.php?taxonomy=mktopics-cats' );		
		/// add_submenu_page( $menu_slug, __( 'Topic\'s tag | %s', 'tify' ), $title ), __( 'Add topic tag', 'tify' ), 'edit_posts', 'edit-tags.php?taxonomy=mktopics-tags' );
	}

	/* = CONTROLEUR = */
	/** == Récupération des options == **/
	function get_option( $option ){
		return $this->options->get_option( $option );			
	}
	
	/** == Récupération de la page d'accroche des forums == **/
	function hook_page_id(){
		return (int) get_option( 'page_for_tify_forum', 0 );
	}
	
	/** == == **/
	function hook_page_permalink(){
		return esc_url( get_permalink( $this->hook_page_id() ) );
	}
}
global $tify_forum;
New tiFy_Forum;