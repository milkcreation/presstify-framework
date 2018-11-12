<?php
new tinymceTable();

class tinymceTable {	
	private $base_url;
	
	public function __construct(){
	    $this->base_url = MKTZR_URL.'/plugins/admin-manager/addons/tinymce-plugins/tinymce-table';
		
		add_action('admin_head', array( &$this, 'admin_head' ) );	
	}
	
	public function admin_head(){
	    $plugin = $this->base_url.'/plugin.min.js';
	
	    new tinymcePlugins(
	        'table',
	        $plugin,
	        null,
	        array()
	    );
		new tinymcePlugins(
	        'tablecontrols',
	        $plugin,
	        null,
	        array()
	    );
	}	
}