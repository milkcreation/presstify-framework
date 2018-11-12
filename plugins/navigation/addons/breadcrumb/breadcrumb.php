<?php
/*
Addon Name: BreadCrumb
Addon URI: http://presstify.com/navigation/addons/breadcrumb
Description: Fil d'Arianne
Version: 1.0.1
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

/**
 * Affichage du fil d'arianne
 */
function mktzr_breadcrumb( $args = array() ) {
    global $post;
   	global $wp_query;

	// Traitement des arguments
	$defaults = array( 
		'sep' => '&nbsp;/&nbsp;',
		'before' => "",
		'after' => "",
		'echo' => true
	);
	$args = apply_filters( 'mktzr_breadcrumb_args', $args );
	$args = wp_parse_args( $args, $defaults );	
	extract( $args );
		
	$separator = apply_filters( 'mktzr_breadcrumb_separator', "<span class=\"sep\">$sep</span>" );
  	
	$output  = "";
	$output .= "<div class=\"mktzr-breadcrumb\">".$before;
	
	// ACCUEIL
	$output .= apply_filters( 'mktzr_breadcrumb_home', sprintf( '<a href="%1$s" title="%3$s">%2$s</a>', home_url(), ( ( $front_page = get_option( 'page_on_front' ) ) ? get_the_title( $front_page ) : __( 'Accueil', 'tify' ) ), __( 'Retour à l\'accueil', 'tify' ) ), $separator );
	// PAGE 404
	if ( is_404() ) : 
		$output .= apply_filters( 'mktzr_breadcrumb_404', sprintf( '%1$s <span class="current">%2$s</span>', $separator, __( 'Erreur 404 - Impossible de trouver la page', 'tify' ) ), $separator );
	// RECHERCHE
	elseif ( is_search() ) : 
		$output .= apply_filters( 'mktzr_breadcrumb_is_search', sprintf( '%1$s<span class="current">%2$s "%3$s"</span>', $separator, __( 'Recherche de' , 'tify' ), get_search_query() ), $separator, $post );
	// TAXONOMIES
	elseif ( is_tax() ):
		$tax = get_queried_object();		
		$output .= apply_filters( 'mktzr_breadcrumb_is_tax',  sprintf( '%1$s<a>%2$s</a>%1$s<span class="current">%3$s</span>', $separator, get_taxonomy( $tax->taxonomy )->label, $tax->name ), $separator, $tax );
	// FRONT PAGE
	elseif ( is_front_page() ) : 
		if( $page_for_posts = get_option( 'page_on_front') ) :
			$output .= apply_filters( 'mktzr_breadcrumb_is_front_page', sprintf( '%1$s<span class="current">%2$s</span>', $separator, esc_html( wp_strip_all_tags( get_the_title( $page_for_posts ) ) ) ), $separator );
		else :
			if( is_paged() ) :
				global $wp_query;
				$output .= apply_filters( 'mktzr_breadcrumb_is_home_paged', sprintf( __('%1$s<span class="current">News page %2$s of %3$s</span>', 'tify'), $separator, ( get_query_var('paged') ? get_query_var('paged') : 1 ), $wp_query->max_num_pages ) , $separator  );
			else :
				$output .= apply_filters( 'mktzr_breadcrumb_is_home', sprintf( '%1$s<span class="current">%2$s</span>', $separator, __( 'All News', 'tify' ) ), $separator );
			endif;
		endif;
	// HOMEPAGE	
	elseif ( is_home() ) :
		if( $page_for_posts = get_option( 'page_for_posts') ) :
			$output .= apply_filters( 'mktzr_breadcrumb_is_front_page', sprintf( '%1$s<span class="current">%2$s</span>', $separator, esc_html( wp_strip_all_tags( get_the_title( $page_for_posts ) ) ) ), $separator );
		else :			
			if( is_paged() ) :
				global $wp_query;
				$output .= apply_filters( 'mktzr_breadcrumb_is_home_paged', sprintf( __('%1$s<span class="current">News page %2$s of %3$s</span>', 'tify'), $separator, ( get_query_var('paged') ? get_query_var('paged') : 1 ), $wp_query->max_num_pages ) , $separator  );
			else :
				$output .= apply_filters( 'mktzr_breadcrumb_is_home', sprintf( '%1$s<span class="current">%2$s</span>', $separator, __( 'All News', 'tify' ) ), $separator );
			endif;
		endif;
	// ATTACHMENT
	elseif ( is_attachment() ) :
		global $post;
		$ancestors = '';
		if( is_singular() && $post->post_parent && $post->ancestors ) :
			$parents = ( count( $post->ancestors ) > 1 ) ? array_reverse( $post->ancestors ) : $post->ancestors;
			if( ( 'post' === get_post_type( current( $parents ) ) )  && ( $page_for_posts = get_option( 'page_for_posts' ) ) )
				$ancestors .= sprintf('%3$s<a href="%1$s">%2$s</a>', get_permalink( $page_for_posts ), esc_html( wp_strip_all_tags( get_the_title( $page_for_posts ) ) ), $separator );

			reset( $parents );
			foreach( $parents as $parent )
				$ancestors .= sprintf('%3$s<a href="%1$s">%2$s</a>', get_permalink( $parent ), esc_html( wp_strip_all_tags( get_the_title( $parent ) ) ), $separator );
		endif;		
		
		$output .= $ancestors. apply_filters( 'mktzr_breadcrumb_is_attachment', sprintf( '%1$s<span class="current">%2$s</span>', $separator, esc_html( wp_strip_all_tags( get_the_title( ) ) ) ), $separator, $ancestors );
	// SINGLE
	elseif ( is_single() ) :		
		// BLOG HOME PAGE
		if( is_singular( 'post' ) ) :
			if( $page_for_posts = get_option( 'page_for_posts') ) :
				$homepage = esc_html( wp_strip_all_tags( get_the_title($page_for_posts) ) );
				$url = get_permalink( $page_for_posts );
			else :
				$homepage = __( 'All the News', 'tify' );
				$url = home_url();			
			endif;	
			
			$output .= apply_filters( 'mktzr_breadcrumb_is_singular_post', sprintf( '%2$s<a href="%1$s" title="'.__( 'Retour à %3$s', 'tify' ).'">%3$s</a>', $url, $separator, $homepage ), $separator, $homepage, $post );
		endif;	
	
		// POST TYPE ARCHIVE LINK
		$post_type_archive_link = '';
		if( $post->post_type != 'post' && ($post_type_obj = get_post_type_object( $post->post_type ) ) && $post_type_obj->has_archive ) :
			$post_type_archive_link = sprintf( '%3$s<a href="%1$s">%2$s</a>', get_post_type_archive_link( $post->post_type ), $post_type_obj->labels->name, $separator );
		endif;	
			
		// POST WITH ANCESTOR
		$ancestors = '';
		if( is_singular() && $post->post_parent && $post->ancestors ) :
			$parents = ( count( $post->ancestors ) > 1 ) ? array_reverse( $post->ancestors ) : $post->ancestors;
			foreach( $parents as $parent )
				$ancestors .= sprintf('%3$s<a href="%1$s">%2$s</a>', get_permalink( $parent ), esc_html( wp_strip_all_tags( get_the_title( $parent ) ) ), $separator );
		endif;

		$output .= apply_filters( 'mktzr_breadcrumb_is_singular', $post_type_archive_link.$ancestors.$separator.'<span class="current">'.esc_html( wp_strip_all_tags( get_the_title() ) ).'</span>', $separator, $ancestors, $post_type_archive_link, $post );
	
	// PAGE
	elseif ( is_page() ) :  
		$ancestors = '';
		// PAGE WITH ANCESTOR
		if( is_singular() && $post->post_parent && $post->ancestors ) :
			$parents = ( count( $post->ancestors ) > 1 ) ? array_reverse( $post->ancestors ) : $post->ancestors;
			foreach( $parents as $parent )
				$ancestors .= sprintf('%3$s<a href="%1$s">%2$s</a>', get_permalink( $parent ), esc_html( wp_strip_all_tags( get_the_title( $parent ) ) ), $separator );
		endif;
	
		$output .= apply_filters( 'mktzr_breadcrumb_is_page', $ancestors.$separator.'<span class="current">'.esc_html( wp_strip_all_tags( get_the_title() ) ).'</span>', $separator, $ancestors );
	
	// CATEGORY
	elseif( is_category() ) :
		if( $category = get_category( get_query_var('cat'), false ) ):
				$output .= apply_filters( 'mktzr_breadcrumb_is_category', sprintf( '%1$s<span class="current">%2$s</span>', $separator, $category->name ), $separator, $category );
		endif;
	
	//** TODO **/
	elseif ( is_tag() ):
		$output .= apply_filters( 'mktzr_breadcrumb_is_tag', sprintf( __( '%1$s<span class="current">Tag : %2$s</span>', 'tify' ), $separator, get_query_var('tag') ), $separator );
	elseif ( is_author() ):
	elseif ( is_date() ) :
		if ( is_day() ) :
			$output .= apply_filters( 'mktzr_breadcrumb_is_daily_archive', sprintf( __( '%1$sDaily Archives: %2$s', 'tify' ), $separator, '<span>' . get_the_date() . '</span>' ), $separator );
		elseif ( is_month() ) :  
			$output .= apply_filters( 'mktzr_breadcrumb_is_monthly_archive', sprintf( __( '%1$sMonthly Archives: %2$s', 'tify' ), $separator, '<span>' . get_the_date( 'F Y' ) . '</span>' ), $separator );
		elseif ( is_year() ) :
			$output .= apply_filters( 'mktzr_breadcrumb_is_yearly_archive', sprintf( __( '%1$sYearly Archives: %2$s', 'tify' ), $separator, '<span>' . get_the_date( 'Y' ) . '</span>' ), $separator );
		endif;
	elseif ( is_archive() )	:	
	// ARCHIVES
		if( is_post_type_archive() ) :
			$output .= apply_filters( 'mktzr_breadcrumb_is_post_type_archive', sprintf( '%1$s<span class="current">%2$s</span>', $separator, post_type_archive_title( '', false ) ), $separator );
		else:
			 $output .= __( 'Blog Archives', 'tify' ); 
		endif;
	
	//** TODO **/
	elseif ( is_comments_popup() ) :
	elseif ( is_paged() ) :
	else :
	endif;		
			
	$output .= "</div>";
	
	if( $echo )
		echo $output;
	else
		return $output;
}