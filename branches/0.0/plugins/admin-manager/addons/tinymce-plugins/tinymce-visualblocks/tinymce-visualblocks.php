<?php
new tinymceVisualBlocks();

class tinymceVisualBlocks{	
	private $base_url;
	
	public function __construct(){
	    $this->base_url = MKTZR_URL.'/plugins/admin-manager/addons/tinymce-plugins/tinymce-visualblocks';
		
		add_action( 'admin_head', array( &$this, 'admin_head' ) );	
	}
	
	public function admin_head(){
	    $plugin = $this->base_url.'/plugin.min.js';
	
	    new tinymcePlugins(
	        'visualblocks',
	        $plugin,
	        null,
	        array()
	    );
	}
}