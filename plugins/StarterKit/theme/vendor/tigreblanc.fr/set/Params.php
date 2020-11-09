<?php
namespace TigreBlanc\Set;

use tiFy\Components;

class Params extends \tiFy\App\Factory
{
    /**
     * Liste des actions à déclencher
     * @var string[]
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference
     */
    protected $tFyAppActions                = array(
        'tify_components_register'
    );

    /**
     * DECLENCHEURS
     */
    /**
     * Déclaration de composants dynamique
     * 
     */
    public function tify_components_register()
    {
        Components::register(
            'AdminUI',
            array(
                'admin_bar_menu_logo'   => array(
                    array(
                        'id'        => 'tb-logo',
                        'title'     => '<span class="tigreblanc-logo" style="font-size:35px;"></span>',
                        'href'      => 'http://www.tigreblanc.fr',
                        'meta'      => array( 
                            'title'     => __( 'A propos de Tigre Blanc', 'tify' ), 
                            'target'    => '_blank' 
                        )                        
                    ),
                    array(
                        'id'        => 'tb-logo-site',
                        'parent'    => 'tb-logo',
                        'title'     => __( 'Site Officiel de Tigre Blanc', 'tify' ),
                        'href'      => 'http://www.tigreblanc.fr'                       
                    ),
                    array(
                        'id'        => 'tb-logo-external',
                        'parent'    => 'tb-logo',
                        'group'     => true,
                        'meta'      => array( 
                            'class' =>  'ab-sub-secondary' 
                        )
                    ),
                    array(
                        'id'        => 'tb-logo-facebook',
                        'parent'    => 'tb-logo-external',
                        'title'     => __( 'Page Facebook', 'tify' ),
                        'href'      => 'http://www.facebook.com/tigreblancdouai',
                        'meta'      => array( 
                            'target' =>  '_blank' 
                        )
                    ),
                    array(
                        'id'        => 'tb-logo-twitter',
                        'parent'    => 'tb-logo-external',
                        'title'     => __( 'Compte Twitter', 'tify' ),
                        'href'      => 'https://twitter.com/TigreBlancDouai',
                        'meta'      => array( 
                            'target' =>  '_blank' 
                        )
                    ),
                    array(
                        'id'        => 'tb-logo-mailing',
                        'parent'    => 'tb-logo',
                        'group'     => true,
                        'meta'      => array( 
                            'class' =>  'ab-sub-primary' 
                        )
                    ),
                    array(
                        'id'        => 'tb-logo-mailing-contact',
                        'parent'    => 'tb-logo-mailing',
                        'title'     => __( 'Contact l\'agence', 'tify' ),
                        'href'      => 'mailto:contact@tigreblanc.fr'
                    ),
                    array(
                        'id'        => 'tb-logo-mailing-support',
                        'parent'    => 'tb-logo-mailing',
                        'title'     => __( 'Support Technique', 'tify' ),
                        'href'      => 'mailto:support@tigreblanc.fr'
                    )
                ),
                'admin_footer_text' =>  __( 'Merci de faire de <a class="tigreblanc-logo" href="http://www.tigreblanc.fr" style="font-size:40px; vertical-align:middle; display:inline-block;" target="_blank"></a> le partenaire de votre communication digitale', 'tify' )
            )
        );
    }
}