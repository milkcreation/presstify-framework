<?php
namespace App;

class GeneralTemplate
{
    /**
     * CORPS DE PAGE
     */
    /**
     * Pré-affichage du corps de page 
     */
    public static function bodyContentBefore() { }
    
    /**
     * Post-affichage du corps de page
     */
    public static function bodyContentAfter() { }

    /**
     * GRILLE BOOTSTRAP
     */
    /**
     * Format du conteneur de la grille du corps de contenu de page (statique par défaut)
     */
    public static function bodyContentGridContainer( $class = 'container' )
    {
        return $class;
    }
    
    /**
     * Largeur de la colonne de la zone de contenu (plein par défaut)
     */
    public static function contentGridCol( $class= 'col-lg-12' )
    {
        return $class;
    }
    
    /**
     * Format du conteneur de la grille du corps de contenu de page (statique par défaut)
     */
    public static function bodyContentGridRow( $class = 'row' )
    {
        return $class;
    }
    
    /**
     * Largeur de la colonne de la sidebar (masquée par défaut)
     */
    public static function sidebarGridCol( $class= 'hidden-lg hidden-md hidden-sm hidden-xs' )
    {
        return $class;
    }
        
    /**
     * CONTENU
     */
    /**
     * Entête du contenu
     */
    public static function contentHeader()
    {        
        if( is_home() ) :
            if( $page_for_posts = get_option( 'page_for_posts' ) ) :
?><h1 class="Content-headerTitle"><?php echo get_the_title( $page_for_posts );?></h1><?php
            else :
?><h1 class="Content-headerTitle"><?php _e( 'Toutes les actualités', 'Theme' );?></h1><?php            
            endif;
        elseif( is_post_type_archive() ) :
?><h1 class="Content-headerTitle"><?php echo post_type_archive_title();?></h1><?php
        endif;
    }
    
    /**
     * Pied de page du contenu
     */
    public static function contentFooter()
    {
        if( is_home() || is_post_type_archive() ) :
            ?><nav class="Content-footerPagination"><?php tify_pagination();?></nav><?php
        elseif( is_singular() ) :
            // Affichage des pages enfants directs
            $title = get_the_title();
            $DirectChilds = new \WP_Query( array( 'post_parent' => get_the_ID(), 'post_type' => get_post_type(), 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC' ), 'posts_per_page' => 24, 'post_status' => 'publish' ) );
            if( $DirectChilds->have_posts() ) : ?>
            <div class="childContent">
                <h2 class="childContent-title"><?php printf( __( 'En relation avec %s', 'Theme' ), $title );?></h2>
                <div class="row">
                <?php while( $DirectChilds->have_posts() ) : $DirectChilds->the_post(); ?>
                    <div class="col-lg-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h2 class="panel-title"><?php the_title();?></h2>
                            </div>
                            <div class="panel-body">
                                <div class="theContent-body">
                                    <?php get_template_part( 'content-archive', get_post_type() );?>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <a class="Article-readmore btn btn-primary" href="<?php the_permalink()?>" title="<?php printf( __( 'Consulter - %s', 'tity' ), get_the_title() );?>"><?php _e( 'Lire la suite', 'Theme' );?></a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata();?>
                </div>
            </div>   
<?php       endif;
        endif;
    }
    
    /**
     * CONTENU DE LA BOUCLE WORDPRESS
     */
    /**
     * Entête de la boucle
     */
    public static function theContentHeader() { }
    
    /**
     * Pied de page de la boucle
     */
    public static function theContentFooter() { }
}