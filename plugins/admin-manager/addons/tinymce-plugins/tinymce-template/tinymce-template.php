<?php
new tinymceTemplates();

class tinymceTemplates {
	private $base_url;

	public function __construct(){
		global $tiFy;
		
		$this->tiFy 	= $tiFy;
		// Définition des chemins
		$this->dir 		= dirname( __FILE__ );
		$this->path  	= $this->tiFy->get_relative_path( $this->dir );
		$this->uri		= $this->tiFy->uri . $this->path;
		
		// Actions et Filtres Wordpress	
	    add_action( 'admin_head', array( $this, 'wp_admin_head' ) );		;
		add_filter( 'mce_css', array( $this, 'wp_mce_css' ) );
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
	    add_action( 'wp_ajax_tinymce_templates', array( $this, 'wp_ajax' ) );
	}
	
	
	
	public function wp_admin_head(){
	    $plugin = $this->uri .'/plugin.min.js';
	
	    $url 	= admin_url( 'admin-ajax.php' );
	    $url 	= add_query_arg( 'action', 'tinymce_templates', $url );
	    $nonce 	= wp_create_nonce( 'tinymce_templates' );
	    $url 	= add_query_arg( 'nonce', $nonce, $url );
	
	    $inits['templates'] = $url;
	
	    new tinymcePlugins(
	        'template',
	        $plugin,
	        null,
	        $inits
	    );
	}
	
	public function wp_init(){
		wp_register_style( 'tify-tinymce_template', $this->uri. '/articles.css', array(), '1.150317' );
	}
	
	public function wp_mce_css( $mce_css ) {
        $mce_css .= ', '. $this->uri.'/editor.css';

        return $mce_css;
    }
	
	public function wp_enqueue_scripts(){
		wp_enqueue_style( 'tify-tinymce_template' );
	}
	
	public function wp_ajax(){
	    nocache_headers();
		header( 'Content-Type: application/x-javascript; charset=UTF-8' );
	    if ( ! wp_verify_nonce( $_GET['nonce'], 'tinymce_templates' ) ) 
	        return;
	   
		$arr = apply_filters( 'tify_tinyMCE_templates', array(
					array( 	"title" 		=> "2 Colonnes : 1/4, 3/4", 
							"description" 	=> "1 colonne d'1/4 et l'autre de 3/4",
							"url" 			=> $this->uri ."/templates/2cols_0.25-0.75.htm"
					),
					array( 	"title" 		=> "2 Colonnes : 1/3, 2/3", 
							"description" 	=> "1 colonne d'1/3 et l'autre de 2/3",
							"url" 			=> $this->uri ."/templates/2cols_0.33-0.66.htm"
					),
					array( 	"title" 		=> "2 Colonnes : 1/2, 1/2", 
							"description" 	=> "1 colonnes d'1/2 et l'autre d'1/2",
							"url" 			=> $this->uri ."/templates/2cols_0.5-0.5.htm"
					),
					array( 	"title" 		=> "2 Colonnes : 2/3, 1/3", 
							"description" 	=> "1 colonne de 2/3 et l'autre d'1/3",
							"url" 			=> $this->uri ."/templates/2cols_0.66-0.33.htm"
					),
					array( 	"title" 		=> "2 Colonnes : 3/4, 1/4", 
							"description" 	=> "1 colonne de 3/4 et l'autre d'1/4",
							"url" 			=> $this->uri ."/templates/2cols_0.75-0.25.htm"
					),
					array( 	"title" 		=> "3 Colonnes : 1/4, 1/4, 1/2", 
							"description" 	=> "1 colonne d'1/4, une d'1/4 et une d'1/2",
							"url" 			=> $this->uri ."/templates/3cols_0.25-0.25-0.5.htm"
					),
					array( 	"title" 		=> "3 Colonnes : 1/4, 1/2, 1/4", 
							"description" 	=> "1 colonne d'1/4, une d'1/2 et une d'1/4",
							"url" 			=> $this->uri ."/templates/3cols_0.25-0.5-0.25.htm"
					),
					array( 	"title" 		=> "3 Colonnes : 1/3, 1/3, 1/3", 
							"description" 	=> "1 colonne d'1/3, une d'1/3 et une d'1/3",
							"url" 			=> $this->uri ."/templates/3cols_0.33-0.33-0.33.htm"
					),
					array( 	"title" 		=> "3 Colonnes : 1/2, 1/4, 1/4", 
							"description" 	=> "1 colonne d'1/2, une d'1/4 et une d'1/4",
							"url" 			=> $this->uri ."/templates/3cols_0.5-0.25-0.25.htm"
					),
					array( 	"title" 		=> "4 Colonnes : 1/4, 1/4, 1/4, 1/4", 
							"description" 	=> "1 colonnes d'1/4, une d'1/4, une d'1/4 et une d'1/4",
							"url" 			=> $this->uri ."/templates/4cols_0.25-0.25-0.25-0.25.htm"
					)
				)
			);
				
	    echo json_encode($arr);
	    exit;
	}
	
	public function addButton( $buttons = array()){
	    array_unshift( $buttons, '|' );
	    array_unshift( $buttons, 'template' );
		
	    return $buttons;
	}	
}