<?php
/**
 * AFFICHAGE GENERAL
 */
use App\GeneralTemplate;

/**
 * CORPS DE PAGE
 */
/**
 * Pré-affichage du corps de page 
 */
function theme_bodycontent_before()
{
    return GeneralTemplate::bodyContentBefore();
}

/**
 * Post-affichage du corps de page
 */
function theme_bodycontent_after()
{
    return GeneralTemplate::bodyContentAfter();
}

/**
 * GRILLE BOOTSTRAP
 */
/**
 * Format du conteneur de la grille du corps de contenu de page (statique par défaut)
 */
function theme_bodycontent_grid_container( $class = 'container' )
{
    return GeneralTemplate::bodyContentGridContainer( $class );
}

/**
 * Format de ligne de la grille du corps de contenu de page (statique par défaut)
 */
function theme_bodycontent_grid_row( $class = 'row' )
{
    return GeneralTemplate::bodyContentGridRow( $class );
}

/**
 * Largeur de la colonne de la zone de contenu (plein par défaut)
 */
function theme_content_grid_col( $class = 'col-lg-12' )
{
    return GeneralTemplate::contentGridCol( $class );
}

/**
 * Largeur de la colonne de la sidebar (masquée par défaut)
 */
function theme_sidebar_grid_col( $class = 'hidden-lg hidden-md hidden-sm hidden-xs' )
{
    return GeneralTemplate::sidebarGridCol( $class );
}

/**
 * CONTENU
 */
/**
 * Entête du contenu
 */
function theme_content_header()
{
    return GeneralTemplate::contentHeader();
}

/**
 * Pied de page du contenu
 */
function theme_content_footer()
{
    return GeneralTemplate::contentFooter();
}

/**
 * CONTENU DE LA BOUCLE WORDPRESS
 */
/**
 * Entête de la boucle
 */
function theme_the_content_header()
{
    return GeneralTemplate::theContentHeader();
}

/**
 * Pied de page de la boucle
 */
function theme_the_content_footer()
{
    return GeneralTemplate::theContentFooter();
}