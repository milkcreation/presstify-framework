<?php
/*
 Plugin Name: Social Share
 Plugin URI: http://presstify.com/plugins/social-share
 Description: Partage sur les réseaux sociaux
 Version: 1.1.385
 Author: Milkcreation
 Author URI: http://milkcreation.fr
 Text Domain: tify
*/

namespace tiFy\Plugins\Social;

use tiFy\App\Plugin;

class Social extends Plugin
{
    /**
     * Liste des actions à déclencher
     * @var string[]
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference
     */
    protected $tFyAppActions                = array(
        'admin_enqueue_scripts',
        'tify_options_register_node'
    );
    
    public static $Options;
    
    /**
     * CONSTRUCTEUR
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Récupération des options enregistrées
        self::$Options = get_option('tify_social_share', array());
        
        // Chargement des réseaux
        foreach ((array) self::tFyAppConfig('Networks') as $networkName => $networkAttrs) :
            $classname = "\\tiFy\\Plugins\\Social\\Networks\\". ucfirst( $networkName ) ."\\". ucfirst( $networkName );
            
            // Bypass - Le réseau n'existe pas
            if (! class_exists($classname))
                continue;
            
            new $classname($networkAttrs);
        endforeach;
    }
    
    /**
     * DECLENCHEURS
     */
    /**
     * Mise en file des scripts de l'interface d'administration
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_style( 'font-awesome' );
    }
    
    /**
     * Déclaration de la boîte à onglets d'administration des options des réseaux sociaux déclarés
     */
    public function tify_options_register_node()
    {
        tify_options_register_node(
            array(
                'id'        => 'tiFyPluginSocial',
                'title'     => __('Réseaux sociaux', 'tify'),
            )
        );
    }
}
