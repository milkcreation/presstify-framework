<?php
namespace App;

class Query extends \tiFy\App\Factory
{
    /**
     * Liste des actions à déclencher
     */
    protected $tFyAppActions                = array(
        'pre_get_posts'        
    );

    /**
     * DECLENCHEURS
     */
    /**
     * 
     */
    final public function pre_get_posts( &$query )
    {    
        // Requêtes l'interface d'administration
        if( is_admin() ) :
        // Requêtes l'interface visiteur
        else :
            /// Requête principale
            if( $query->is_main_query() ) :
                if( $query->is_post_type_archive() ) :

                endif;
            /// Requêtes secondaire
            else :
            
            endif;
        endif;
    }
}