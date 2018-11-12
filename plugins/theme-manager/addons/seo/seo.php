<?php
/*
Addon Name: SEO
Addon URI: http://presstify.com/theme-manager/addons/seo
Description: Gestionnaire de référencement de site
Version: 1.150701
Author: Milkcreation
Author URI: http://milkcreation.fr
*/
class tiFy_SEO{
	/* = ARGUMENTS = */
	public	// Chemins
			$dir,
			$uri,
			
			// Configuration
			$options,
			$post_types = array(),
			
			$ua_code,
			$append_blogname = true;
		
	/* = CONSTRUCTEUR = */
	function __construct(){
		// Définition des chemins
		$this->dir = dirname( __FILE__ );
		$this->uri = plugin_dir_url( __FILE__ );		
		
		// Actions et Filtres Wordpress
		add_action( 'after_setup_theme', array( $this, 'wp_after_setup_theme' ) );
		add_action( 'init', array( $this, 'wp_init' ), 99 );
		add_filter( 'language_attributes', array( $this, 'wp_language_attributes' ) );
		add_action( 'wp_head', array( $this, 'wp_head_first' ), 1 );		
		add_action( 'wp_head', array( $this, 'wp_head_opengraph' ), 5 );		
		add_action( 'wp_head', array( $this, 'wp_head_last' ), 99 );
		add_filter( 'wp_title', array( $this, 'wp_title' ), null, 3 );
				
		// Actions et Filtres PressTiFy
		add_action( 'tify_taboox_register_node', array( $this, 'tify_taboox_register_node' ) );
		add_action( 'tify_taboox_register_form', array( $this, 'tify_taboox_register_form' ) );		
		
		// Actions et Filtres tiFy_seo
		add_action( 'tify_seo_wp_head', array( $this, 'meta_description' ), 10 );
		add_filter( 'tify_seo_title_is_page_on_front', array( $this, '_title_for_singular' ), null, 3 );		
		add_filter( 'tify_seo_title_is_single', array( $this, '_title_for_singular' ), null, 3 );
		add_filter( 'tify_seo_title_is_page', array( $this, '_title_for_singular' ), null, 3 );		
		add_filter( 'tify_seo_title_is_page_for_posts', array( $this, '_title_for_archive' ), null, 3 );
		add_filter( 'tify_seo_desc_is_page_on_front', array( $this, '_desc_for_singular' ), null, 3 );
		add_filter( 'tify_seo_desc_is_single', array( $this, '_desc_for_singular' ), null, 3 );
		add_filter( 'tify_seo_desc_is_page', array( $this, '_desc_for_singular' ), null, 3 );			
	}
	
	/* = CONFIGURATION = */
	/** == Définition des options == **/
	function set_options(){
		$this->options = apply_filters( 'tify_seo_options', array( 'taboox_auto' => true ) );
	}
	/** == Définition des types de post == **/
	function set_post_types(){
		$defaults = array(
			'taboox_auto'	=> true,				// Déclaration automatique de la boîte de sasie
		);
		$post_types = apply_filters( 'tify_seo_post_types', array_diff( get_post_types(), array( 'attachment', 'revision', 'nav_menu_item' ) ) );
		
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
		$this->ua_code	= get_option( 'tify_google_analytics_ua_code', false );
		
		$this->set_options();		
	}
	
	/** == Initialisation globale == **/
	function wp_init(){
		$this->set_post_types();
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
		remove_action( 'wp_head', 'wp_dlmp_l10n_style' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );	
	}
	
	/** == == **/
	function wp_language_attributes( $output ){
		if( is_admin() )
			return $output;
		return $output . ' xmlns:og="http://opengraphprotocol.org/schema/"';
	}
		
	/** == == **/
	function wp_head_first(){	
		do_action( 'tify_seo_wp_head' );
	}
	
	/** == Balises Meta de l'Opengraph == **/
	function wp_head_opengraph( $output ){
		$tify_opengraph = get_option( 'tify_opengraph' );
		if( ! isset( $tify_opengraph['active'] ) || ( $tify_opengraph['active'] !== 'on' ) )
			return $output;

		if( is_front_page() ) :
			$page_on_front = false;
			if( $page_on_front =  get_option( 'page_on_front' ) )
				$post = get_post( $page_on_front );
				
			if( $page_on_front )
				$output .= '<meta property="og:title" content="' . get_the_title( $post->ID ) .'"/>';
			else
				$output .= '<meta property="og:title" content="' . get_bloginfo('name') .'"/>';
				
			$output .= '<meta property="og:site_name" content="' . get_bloginfo('name') .' | '. get_bloginfo('description') .'"/>';
			$output .= '<meta property="og:url" content="' . home_url() . '"/>';
				
			if( $page_on_front && $post->post_excerpt )
				$output .= '<meta property="og:description" content="' . apply_filters( 'get_the_excerpt', $post->post_excerpt ) . '" />';
			else
				$output .= '<meta property="og:description" content="' . get_bloginfo('description') . '" />';
				
			if( $image = tify_custom_attachment_image( $tify_opengraph['default_image'], array( 1200, 1200, false ) ) )
				$output .= '<meta property="og:image" content="' . esc_attr( $image['url'] .'/'. $image['file'] ) . '"/>';
			elseif( $image = tify_custom_attachment_image( $tify_opengraph['default_image'], array( 600, 600, false ) ) )
				$output .= '<meta property="og:image" content="' . esc_attr( $image['url'] .'/'. $image['file'] ) . '"/>';

			$output .= '<meta property="og:type" content="website"/>';
		elseif( is_singular() ) :
			$output .= '<meta property="og:title" content="' . get_the_title() . '"/>';
			$output .= '<meta property="og:site_name" content="'.get_bloginfo('name').'"/>';
			$output .= '<meta property="og:url" content="' . get_permalink() . '"/>';
			$output .= '<meta property="og:description" content="' . esc_attr( strip_tags( get_the_excerpt() ) ). '" />';
			if( $image = tify_custom_attachment_image( get_post_thumbnail_id( get_the_ID() ), array( 1200, 1200, false ) ) ) :
				$output .= '<meta property="og:image" content="' . esc_attr( $image['url'] .'/'. $image['file'] ) . '"/>';
			elseif( $image = tify_custom_attachment_image( get_post_thumbnail_id( get_the_ID() ), array( 600, 600, false ) ) ) :
				$output .= '<meta property="og:image" content="' . esc_attr( $image['url'] .'/'. $image['file'] ) . '"/>';
			elseif( $image = tify_custom_attachment_image( $tify_opengraph['default_image'], array( 1200, 1200, true ) ) ) :
				$output .= '<meta property="og:image" content="' . esc_attr( $image['url'] .'/'. $image['file'] ) . '"/>';
			elseif( $image = tify_custom_attachment_image( $tify_opengraph['default_image'], array( 600, 600, true ) ) ) :
				$output .= '<meta property="og:image" content="' . esc_attr( $image['url'] .'/'. $image['file'] ) . '"/>';
			endif;
			$output .= '<meta property="og:type" content="article"/>';
		endif;
		
		echo $output;
	}

	/** == == **/
	function wp_head_last(){		
		// Bypass
		if( ! $this->ua_code )
			return;
	?><script type="text/javascript">/* <![CDATA[ */(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '<?php echo $this->ua_code;?>', 'auto');ga('send', 'pageview');/* ]]> */</script><?php
	}
	
	/** == Balise Titre des pages du site == **/
	function wp_title( $title, $sep, $seplocation ){
		if ( is_feed() )
			return $title;
		
		$blogname 			= get_bloginfo( 'name' );
		$site_description 	= get_bloginfo( 'description', 'display' );
		
		// PAGE 404
		if ( is_404() ) :
			$title = apply_filters( 'tify_seo_title_is_404', __( 'Erreur 404 - Impossible de trouver la page', 'tify' ), $sep, $seplocation );
		// RECHERCHE
		elseif ( is_search() ) : 
			$title = apply_filters( 'tify_seo_title_is_search', sprintf( '%1$s %2$s', __( 'Recherche de' , 'tify' ), get_search_query() ), $sep, $seplocation );
		// TAXONOMIES
		elseif ( is_tax() ):
			$tax = get_queried_object();			
			$title = apply_filters( 'tify_seo_title_is_tax',  sprintf( '%1$s %2$s %3$s', get_taxonomy( $tax->taxonomy )->label, $sep, $tax->name ), $sep, $seplocation );
		// FRONT PAGE
		elseif ( is_front_page() ) :
			if( $page_for_posts = get_option( 'page_on_front' ) ) :
				$_title = esc_html( wp_strip_all_tags( get_the_title( $page_for_posts ) ) );
				$title = apply_filters( 'tify_seo_title_is_page_on_front', $_title, $sep, $seplocation );
				$this->append_blogname = ( $_title === $title ) ? $this->append_blogname : false;
			else :
				if( is_paged() ) :
					global $wp_query;
					$title = apply_filters( 'tify_seo_title_is_front_paged', sprintf( __( 'Actualités page %1$s sur %2$s', 'tify' ), ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 ), $wp_query->max_num_pages ) , $sep, $seplocation );
				else :
					$title = apply_filters( 'tify_seo_title_is_front_page', __( 'Actualités', 'tify' ), $sep, $seplocation );
				endif;
			endif;
		// HOME PAGE	
		elseif ( is_home() ) :			
			if( $page_for_posts = get_option( 'page_for_posts' ) ) :
				$title = apply_filters( 'tify_seo_title_is_page_for_posts', esc_html( wp_strip_all_tags( get_the_title( $page_for_posts ) ) ), $sep, $seplocation );
			else :			
				if( is_paged() ) :
					global $wp_query;
					$title = apply_filters( 'tify_seo_title_is_home_paged', sprintf( __( 'Actualités page %1$s sur %2$s', 'tify'),( get_query_var('paged') ? get_query_var('paged') : 1 ), $wp_query->max_num_pages ) , $sep, $seplocation );
				else :
					$title = apply_filters( 'tify_seo_title_is_home', __( 'Actualités', 'tify' ), $sep, $seplocation );
				endif;
			endif;
		// ATTACHMENT
		elseif ( is_attachment() ) :
			$title = apply_filters( 'tify_seo_title_is_attachment', esc_html( wp_strip_all_tags( get_the_title( ) ) ), $sep, $seplocation );
		// SINGLE
		elseif ( is_single() ) :
			$_title = esc_html( wp_strip_all_tags( get_the_title() ) );		
			$title = apply_filters( 'tify_seo_title_is_single', $_title, $sep, $seplocation );	
			$this->append_blogname = ( $_title === $title ) ? $this->append_blogname : false;
		// PAGE
		elseif ( is_page() ) :
			$_title = esc_html( wp_strip_all_tags( get_the_title() ) );	
			$title = apply_filters( 'tify_seo_title_is_page', $_title, $sep, $seplocation );
			$this->append_blogname = ( $_title === $title ) ? $this->append_blogname : false;		
		// CATEGORY
		elseif( is_category() ) :
			if( $category = get_category( get_query_var('cat'), false ) ):
				$title = apply_filters( 'tify_seo_title_is_category', $category->name, $sep, $seplocation );
			endif;		
		// TAG
		elseif ( is_tag() ):
			$title = apply_filters( 'tify_seo_title_is_tag', sprintf( __( 'Mot clef : %1$s', 'tify' ), get_query_var('tag') ), $sep, $seplocation );
		// AUTHOR
		elseif ( is_author() ):
		// DATE
		elseif ( is_date() ) :
			if ( is_day() ) :
				$title = apply_filters( 'tify_seo_title_is_day', sprintf( __( 'Archive quotidienne : %1$s', 'tify' ), get_the_date() ), $sep, $seplocation );
			elseif ( is_month() ) :  
				$title = apply_filters( 'tify_seo_title_is_month', sprintf( __( 'Archive mensuelle : %1$s', 'tify' ), get_the_date( 'F Y' ) ), $sep, $seplocation );
			elseif ( is_year() ) :
				$title = apply_filters( 'tify_seo_title_is_year', sprintf( __( 'Archive annuelle : %1$s', 'tify' ), get_the_date( 'Y' )  ), $sep, $seplocation );
			endif;
		// ARCHIVES
		elseif ( is_archive() )	:		
			if( is_post_type_archive() ) :
				$title = apply_filters( 'tify_seo_title_post_type_archive', post_type_archive_title( '', false ), $sep, $seplocation );
			else:
				$title = __( 'Archives', 'tify' ); 
			endif;		
		//** TODO **/
		elseif ( is_comments_popup() ) :
		elseif ( is_paged() ) :
		else :
		endif;		

		return $title .( $this->append_blogname ? " $sep ". $blogname: '' );
	}
	
	/* = ACTIONS ET FILTRES PressTiFy = */
	/** == Déclaration des taboox == **/
	function tify_taboox_register_node(){
		foreach( (array) $this->post_types as $post_type => $args )
			if( $args['taboox_auto'] )
				tify_taboox_register_node_post( 
					$this->get_post_types( ), 
					array( 
						'id' 		=> 'tify_seo_postmetatag',
						'title' 	=> __( 'Référencement', 'tify' ), 
						'cb' 		=> 'tiFy_SEO_PostMetaTag_Taboox' 
					) 
				);
				
		if( $this->options['taboox_auto'] ) :
			tify_options_register_node(
				array(
					'id' 		=> 'tify_seo_options',
					'title' 	=> __( 'Référencement', 'tify' ),
				)
			);
			tify_options_register_node(
				array(
					'parent'	=> 'tify_seo_options',
					'id' 		=> 'tify_seo_opengraph',
					'title' 	=> __( 'Metadonnées de l\'Opengraph', 'tify' ),
					'cb' 		=> 'tiFy_SEO_OpenGraph_Taboox',
					'order'		=> 1
				)
			);
			tify_options_register_node(
				array(
					'parent'	=> 'tify_seo_options',
					'id' 		=> 'tify_seo_google-analytics',
					'title' 	=> __( 'Google Analytics', 'tify' ),
					'cb' 		=> 'tiFy_SEO_GoogleAnalytics_Taboox',
					'order'		=> 2
				)
			);
		endif;		
	}
	/** == Déclaration des taboox == **/
	function tify_taboox_register_form(){		
		tify_taboox_register_form( 'tiFy_SEO_OpenGraph_Taboox' );
		tify_taboox_register_form( 'tiFy_SEO_GoogleAnalytics_Taboox' );
		tify_taboox_register_form( 'tiFy_SEO_PostMetaTag_Taboox', $this );
	}
	
	/* = ACTIONS ET FILTRES TiFy_SEO = */
	/** == Balise meta description des pages du site == **/
	function meta_description(){
		// PAGE 404
		if ( is_404() ) :
			$desc = apply_filters( 'tify_seo_desc_is_404', __( 'Erreur 404 - Impossible de trouver la page', 'tify' ) );
		// RECHERCHE
		elseif ( is_search() ) : 
			$desc = apply_filters( 'tify_seo_desc_is_search', sprintf( '%1$s %2$s', __( 'Recherche de' , 'tify' ), get_search_query() ) );
		// TAXONOMIES
		elseif ( is_tax() ):
			$tax = get_queried_object();			
			$desc = apply_filters( 'tify_seo_desc_is_tax',  sprintf( '%1$s %2$s', get_taxonomy( $tax->taxonomy )->label, $tax->name ) );
		// FRONT PAGE
		elseif ( is_front_page() ) :
			if( $page_for_posts = get_option( 'page_on_front' ) ) :
				$desc = apply_filters( 'tify_seo_desc_is_page_on_front', $this->get_singular_desc( $page_for_posts ) );
			else :
				if( is_paged() ) :
					global $wp_query;
					$desc = apply_filters( 'tify_seo_desc_is_front_paged', sprintf( __( 'Actualités page %1$s sur %2$s', 'tify' ), ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 ), $wp_query->max_num_pages )  );
				else :
					$desc = apply_filters( 'tify_seo_desc_is_front_page', __( 'Actualités', 'tify' ) );
				endif;
			endif;
		// HOME PAGE	
		elseif ( is_home() ) :			
			if( $page_for_posts = get_option( 'page_for_posts' ) ) :
				$desc = apply_filters( 'tify_seo_desc_is_page_for_posts', $this->get_singular_desc( $page_for_posts ) );
			else :			
				if( is_paged() ) :
					global $wp_query;
					$desc = apply_filters( 'tify_seo_desc_is_home_paged', sprintf( __( 'Actualités page %1$s sur %2$s', 'tify'),( get_query_var('paged') ? get_query_var('paged') : 1 ), $wp_query->max_num_pages )  );
				else :
					$desc = apply_filters( 'tify_seo_desc_is_home', __( 'Actualités', 'tify' ) );
				endif;
			endif;
		// ATTACHMENT
		elseif ( is_attachment() ) :
			$desc = apply_filters( 'tify_seo_desc_is_attachment', esc_html( wp_strip_all_tags( get_the_title( ) ) ) );
		// SINGLE
		elseif ( is_single() ) :		
			$desc = apply_filters( 'tify_seo_desc_is_single',  $this->get_singular_desc( get_the_ID() ) );		
		// PAGE
		elseif ( is_page() ) :
			$desc = apply_filters( 'tify_seo_desc_is_page', $this->get_singular_desc( get_the_ID() ) );		
		// CATEGORY
		elseif( is_category() ) :
			if( $category = get_category( get_query_var('cat'), false ) ):
				$desc = apply_filters( 'tify_seo_desc_is_category', $category->name );
			endif;		
		// TAG
		elseif ( is_tag() ):
			$desc = apply_filters( 'tify_seo_desc_is_tag', sprintf( __( 'Mot clef : %1$s', 'tify' ), get_query_var('tag') ) );
		// AUTHOR
		elseif ( is_author() ):
		// DATE
		elseif ( is_date() ) :
			if ( is_day() ) :
				$desc = apply_filters( 'tify_seo_desc_is_day', sprintf( __( 'Archive quotidienne : %1$s', 'tify' ), get_the_date() ) );
			elseif ( is_month() ) :  
				$desc = apply_filters( 'tify_seo_desc_is_month', sprintf( __( 'Archive mensuelle : %1$s', 'tify' ), get_the_date( 'F Y' ) ) );
			elseif ( is_year() ) :
				$desc = apply_filters( 'tify_seo_desc_is_year', sprintf( __( 'Archive annuelle : %1$s', 'tify' ), get_the_date( 'Y' )  ) );
			endif;
		// ARCHIVES
		elseif ( is_archive() )	:		
			if( is_post_type_archive() ) :
				$desc = apply_filters( 'tify_seo_desc_post_type_archive', post_type_archive_title( '', false ) );
			else:
				$desc = __( 'Archives', 'tify' ); 
			endif;		
		//** TODO **/
		elseif ( is_comments_popup() ) :
		elseif ( is_paged() ) :
		else :
		endif;
		
		echo "<meta name=\"description\" content=\"". esc_attr( strip_tags( stripslashes( $desc ) ) ) ."\"/>"; 
	}
	
	function get_singular_desc( $post_id ){
		// Bypass
		if( ! $post = get_post( $post_id ) )
			return;
		/// Description
		$desc = get_bloginfo( 'name' ) .'&nbsp;|&nbsp;'. get_bloginfo( 'description' );
		if( $post->post_excerpt )
			$desc = tify_excerpt( strip_tags( $post->post_excerpt ), array( 'max' => 156 ) );
		elseif( $post->post_content )
			$desc = tify_excerpt( strip_tags( $post->post_content ), array( 'max' => 156 ) );
		
		return esc_html( wp_strip_all_tags( $desc ) );
	}
	
	/** == == **/
	function _title_for_singular( $title ){
		global $post;
		
		if( ( $seo_meta = get_post_meta( $post->ID, '_seo_meta', true ) ) && ! empty( $seo_meta['title'] ) )
			return $seo_meta['title'];
			
		return $title;		
	}
	/** == == **/
	function _title_for_archive( $title ){
		global $post;

		if( is_home() && ( $page_for_posts = get_option( 'page_for_posts' ) ) && ( $seo_meta = get_post_meta( $page_for_posts, '_seo_meta', true ) ) && ! empty( $seo_meta['title'] ) )
			return $seo_meta['title'];
		
		return $title;		
	}
	/** == == **/
	function _desc_for_singular( $desc ){
		global $post;
		
		if( ( $seo_meta = get_post_meta( $post->ID, '_seo_meta', true ) ) && ! empty( $seo_meta['desc'] ) )
			return $seo_meta['desc'];
			
		return $desc;		
	}
	/** == == **/
	function _desc_for_archive( $desc ){
		global $post;

		if( is_home() && ( $page_for_posts = get_option( 'page_for_posts' ) ) && ( $seo_meta = get_post_meta( $page_for_posts, '_seo_meta', true ) ) && ! empty( $seo_meta['desc'] ) )
			return $seo_meta['desc'];
		
		return $desc;		
	}
	
	/**
	 * @TODO SCHEMA.ORG
	 */
	/**
	 * Langage Attribute de la balise HTML pour Schema.org
	 * @TODO
	 */
	function schema_language_attributes( $output ){
		if( is_admin() )
			return $output;
		return $output . ' itemscope itemtype="http://schema.org/Article"';
	}
	
	/**
	 * Balises Meta de Schema.org
	 * @TODO
	 */
	function schema_wp_head(){
		if ( ! is_singular() && ! is_front_page() )
			return;
	
		if( is_front_page() ) :
		$page_on_front = false;
		if( $page_on_front =  get_option( 'page_on_front' ) )
			$post = get_post( $page_on_front );
			
		if( $page_on_front )
			echo '<meta itemprop="name" content="' . get_the_title( $post->ID ) .'"/>';
		else
			echo '<meta itemprop="name" content="' . get_bloginfo('name') .'"/>';
			
		if( $page_on_front && $post->post_excerpt )
			echo '<meta itemprop="description" content="'. apply_filters( 'get_the_excerpt', $post->post_excerpt ) .'" />';
		else
			echo '<meta itemprop="description" content="'. get_bloginfo('description') .'" />';
	
		else :
		echo '<meta itemprop="name" content="' . get_the_title() . '"/>';
	
		echo '<meta itemprop="description" content="' .strip_tags( get_the_excerpt() ) . '" />';
		if( $src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' ) ) :
		echo '<meta itemprop="image" content="' . esc_attr( $src[0] ) . '"/>';
		elseif(  $src = $this->get_image_src( $this->options['default_image'] ) ) :
		echo '<meta itemprop="image" content="' . esc_attr( $src ) . '"/>';
		endif;
		endif;
	}
}
new tiFy_SEO;

/* = TABOOX = */
/** == == **/
class tiFy_SEO_OpenGraph_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	$name = 'tify_opengraph',
			$defaults = array( 'active' => 'off', 'default_image' => 0 );
	
	/* = CONSTRUCTEUR = */
	function __construct( ){	
		parent::__construct(
			array(
				'environnements'	=> array( 'option' ),
				'dir'				=> dirname( __FILE__ ),
				'instances'  		=> 1
			)
		);		
	}
	
	/* = INTERFACE D'ADMIN = */
	/** == Mise en file des scripts == **/
	function enqueue_scripts( ){
		wp_enqueue_media();
		tify_controls_enqueue( 'switch' );
		tify_controls_enqueue( 'media_image' );
	}
	
	/** == Formulaire de saisie == **/
	function form( $args = array() ){
	?>
		<table class="form-table">
			<tbody>			
				<tr>
					<th scope="row">
						<label for="tify_social_share-og_active"><?php _e( 'Activer l\'OpenGraph', 'tify' );?></label><br>
					</th>
					<td>
						<?php tify_control_switch( array( 'name' => $this->name .'[active]', 'checked' => $this->value['active'] ) );?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e( 'Image représentative du site (pour le partage)', 'tify' );?><br>
						<em style="font-size:11px; color:#999;"><?php printf( __( 'HD : %s | LD : %s', 'tify' ), '[1200x1200]', '[600x600]')?></em>
					</th>
					<td>
						<?php tify_control_media_image( 
							array( 
								'name' 		=> $this->name .'[default_image]',
								'value'		=> $this->value['default_image'],
								'width'		=> 300,
								'height'	=> 300
							)
						);?>
					</td>
				</tr>
			</tbody>
		</table>
	<?php
	}
}

/** == == **/
class tiFy_SEO_GoogleAnalytics_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	$name = 'tify_google_analytics_ua_code', 
			$value;
	
	/* = CONSTRUCTEUR = */
	function __construct( ){	
		parent::__construct(
			array(
				'environnements'	=> array( 'option' ),
				'dir'				=> dirname( __FILE__ ),
				'instances'  		=> 1
			)
		);		
	}
	
	/* = INTERFACE D'ADMIN = */
	/** == Formulaire de saisie == **/
	function form( $args = array() ){
	?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<?php _e( 'Code Google Analytics', 'bigben' );?><br>
						<em><a href="http://www.google.com/analytics/" title="<?php _e( 'Vers le site officiel de Google Analytics', 'tify' );?>" style="font-size:11px; text-decoration:none;" target="_blank"><?php _e( 'Site Google Analytics', 'tify' );?></a></em>
					</th>			
					<td>
						<input type="text" name="tify_google_analytics_ua_code" value="<?php echo get_option( 'tify_google_analytics_ua_code', false );?>"/>
					</td>
				</tr>
			</tbody>
		</table>
	<?php
	}
}

/** == Réglage des balises Méta du site == **/
class tiFy_SEO_PostMetaTag_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	$name = 'seo_meta',
			// Référence
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_SEO $master  ){
		$this->master = $master;
		parent::__construct(
			// Options
			array(
				'environnements'	=> array( 'post' ),
				'dir'				=> dirname( __FILE__ ),
				'instances'  		=> 1
			)
		);		
	}
	
	/* = INTERFACE D'ADMIN = */
	/** == Déclaration des scripts == **/
	function register_scripts(){
		wp_register_style( 'tify_seo_meta-taboox', $this->uri ."/seo_meta-taboox.css", array( 'tify_controls-text_remaining' ), '150323' );
		wp_register_script( 'tify_seo_meta-taboox', $this->uri ."/seo_meta-taboox.js", array( 'jquery', 'tify_controls-text_remaining' ), '150323', true );
	}
	
	/** == Mise en file des scripts == **/
	function enqueue_scripts( ){
		wp_enqueue_style( 'tify_seo_meta-taboox' );
		wp_enqueue_script( 'tify_seo_meta-taboox' );
	}
	
	/** == Formulaire de saisie == **/
	function form( $args = array() ){
		$defaults = array( 
			'title' => '',
			'url'	=> '',
			'desc'	=> ''
		);
		$this->value = wp_parse_args( $this->value, $defaults );
		// Valeurs originales
		/// Titre		
		$original_title = esc_attr( $this->post->post_title );
		/// Url
		list( $permalink, $post_name ) = get_sample_permalink( $this->post->ID );
		$original_url 	= str_replace( array( '%pagename%', '%postname%' ), $post_name, urldecode( $permalink ) );
		/// Description
		$original_desc = get_bloginfo( 'name' ) .'&nbsp;|&nbsp;'. get_bloginfo( 'description' );
		if( $this->post->post_excerpt )
			$original_desc = tify_excerpt( strip_tags( html_entity_decode( $this->post->post_excerpt ) ), array( 'max' => 156 ) );
		elseif( $this->post->post_content )
			$original_desc = tify_excerpt( strip_tags( html_entity_decode( $this->post->post_content ) ), array( 'max' => 156 ) );
	?>
		<div id="tify_seo_taboox_meta">
			<div id="tify_seo_meta-preview">
				<?php $title = $this->value['title'] ? esc_attr( $this->value['title'] ) : $original_title;?>
				<span id="tify_seo_meta_title-preview" data-original="<?php echo $original_title;?>"><?php echo $title;?></span>

				<?php $url = $this->value['url'] ? esc_url( $this->value['url'] ) : $original_url;?>
				<span id="tify_seo_meta_url-preview" data-original="<?php echo $original_url;?>"><?php echo $url;?></span>
				
				<?php $desc = $this->value['desc'] ? esc_attr( tify_excerpt( $this->value['desc'] ) ) : strip_tags( html_entity_decode( $original_desc ) );?>
				<p id="tify_seo_meta_desc-preview" data-original="<?php echo $original_desc;?>"><?php echo $desc;?></p>
			</div>
			<h3><?php _e( 'Personnalisation', 'tify' );?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php _e( 'Balise titre', 'tify' );?><br>
						</th>
						<td>
							<input type="text" id="tify_seo_meta_title" data-fill_out="#tify_seo_meta_title-preview" name="<?php echo $this->name;?>[title]" placeholder="<?php echo $original_title;?>" value="<?php echo $this->value['title'];?>" autocomplete="off">
						</td>
					</tr>
						<tr>
						<th scope="row">
							<?php _e( 'Url canonique', 'tify' );?><br>
						</th>
						<td>
							<input type="text" id="tify_seo_meta_url" data-fill_out="#tify_seo_meta_url-preview" name="<?php echo $this->name;?>[url]" placeholder="<?php echo $original_url;?>" value="<?php echo $this->value['url'];?>" autocomplete="off">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e( 'Balise description', 'tify' );?><br>
						</th>
						<td>
							<?php tify_control_text_remaining( 
									array( 
										'container_id'	=> 'tify_seo_meta_desc-wrapper', 
										'id'			=> 'tify_seo_meta_desc', 
										'name' 			=> $this->name .'[desc]',
										'value'			=> $this->value['desc'],
										'length' 		=> 156,
										'maxlength'		=> true,
										'attrs' 		=> array( 
											'data-fill_out' => '#tify_seo_meta_desc-preview',
											'placeholder'	=> $original_desc
										) 
									) 
								);?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php
	}
}