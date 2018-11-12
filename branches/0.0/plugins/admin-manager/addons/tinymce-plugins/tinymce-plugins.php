<?php
/*
Addon Name: TinyMCE Plugins
Addon URI: http://presstify.com/admin-manager/addons/tinymce-plugins
Description: Plugins TinyMCE
Version: 1.0.1
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

foreach( array( 'visualblocks', 'template', 'table', 'dashicons', 'fontawesome'/*, 'glyphicon'*/, 'own_glyphs' ) as $plugin )
	require_once( dirname(__FILE__)."/tinymce-{$plugin}/tinymce-{$plugin}.php" );

/**
 * Récupération de la liste des plugins actifs
 */
function tify_tinymceplugins_get_active(){
	return apply_filters( 'tify_tinymceplugins_active', array( 'visualblocks', 'template', 'table' , 'dashicons', 'fontawesome' ) );
}

/**
 * Classe d'appel des plugins
 */
if ( ! class_exists('tinymcePlugins') ) :
class tinymcePlugins{
    private $name = null;
    private $url = null;
    private $inits = array();

    function __construct( $plugin_name, $plugin_url, $button_callback = null, $inits = array() ){
        $this->name = $plugin_name;
        $this->url = $plugin_url;
        add_filter( 'mce_external_plugins', array( $this, 'external_plugins' ) );
        if ($inits) {
            $this->inits = $inits;
            add_filter( 'tiny_mce_before_init', array( $this, 'before_init' ) );
        }
        if ( $button_callback ) {
            add_filter( 'mce_buttons', $button_callback );
        }
    }

    public function before_init($inits){
        foreach ($this->inits as $key => $value) {
            $inits[$key] = $value;
        }
        return $inits;
    }

    public function external_plugins($plugins = array() ){
        $plugins[$this->name] = $this->url;
        return $plugins;
    }
}
endif;