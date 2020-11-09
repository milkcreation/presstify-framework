<?php
namespace PresstiFy\Set\Templates\Jumbotron;

class Jumbotron extends \tiFy\App\Factory
{
    // Intitulés des prefixes des fonctions     
    protected $HelperPrefix         = 'theme';
    
    // Identifiant des fonctions d'aide au développement        
    protected $HelperNamespace      = 'jumbotron';
    
    // Séparateur des parties du nom de la fonction
    protected $HelperSeparator      = '_';
    
    // Liste des methodes à translater en Helpers
    protected $HelperMethods        = array( 'display' );
        
    /* = CONTROLEURS = */
    /** == Affichage == **/
    public static function display()
    {
?>
<div class="jumbotron" style="background-image: url(<?php echo ( new \RedeyeVentures\GeoPattern\GeoPattern( array( 'string' => get_bloginfo( 'name' ) .' - '. get_bloginfo( 'description' ),'baseColor' => '#cb2d3e', 'color' => '#ef473a', 'generator' => 'hexagons' ) ) )->toDataURI();?>);">
    <div class="container">
        <div class="row">
<?php if( is_front_page() ) : ?>
        <div class="col-lg-10">
            <h1 class="Jumbotron-Title"><?php echo get_bloginfo( 'name' );?></h1> 
            <h2 class="Jumbotron-Subtitle"><?php echo get_bloginfo( 'description' );?></h2>
        </div>
        <div class="col-lg-2">
            <div class="Jumbotron-Icon SvgResponsiveContainer">
                <?php include get_template_directory(). '/images/logo.svg';?>
            </div>
        </div>
<?php elseif( is_singular() ) :?>
        <div class="col-lg-10">
            <h1 class="Jumbotron-Title"><?php the_title();?></h1> 
            <h2 class="Jumbotron-Subtitle"><?php the_subtitle();?></h2>
        </div>
        <div class="col-lg-2">
            <div class="Jumbotron-Icon SvgResponsiveContainer">
                <?php include get_template_directory(). '/images/logo.svg';?>
            </div>
        </div>
<?php elseif( is_post_type_archive() ) :?>
    <?php 
        if( $hook_id = get_query_var( 'tify_hook_id' ) ) :
            $title = get_the_title( $hook_id );
            $subtitle = get_the_subtitle( $hook_id );
        else :
            $title = post_type_archive_title();
            $subtitle = '';
        endif;
    ?>
        <div class="col-lg-10">
            <h1 class="Jumbotron-Title"><?php echo $title;?></h1> 
            <h2 class="Jumbotron-Subtitle"><?php echo $subtitle;?></h2>
        </div>
        <div class="col-lg-2">
            <div class="Jumbotron-Icon SvgResponsiveContainer">
                <?php include get_template_directory(). '/images/logo.svg';?>
            </div>
        </div>
<?php endif;?>
        </div>
    </div>
</div>
<?php
    }
}