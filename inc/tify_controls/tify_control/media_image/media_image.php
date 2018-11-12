<?php
class tify_control_media_image extends tify_control{
	/* = Constructeur = */
	function __construct( $master ){
		$this->dir = dirname( __FILE__ );
		parent::__construct( $master );
	}
		
	/* = Déclaration des scripts = */
	function register_scripts(){
		wp_register_style( 'tify_controls-media_image', $this->uri ."/media_image.css", array( ), '141212' );
		wp_register_script( 'tify_controls-media_image', $this->uri ."/media_image.js", array( 'jquery' ), '141212', true );
	}
	
	/* = Initialisation des scripts = */
	function enqueue_scripts(){
		wp_enqueue_media();
	}
		
	/* = Affichage du controleur = */
	function display( $args = array() ){
		if( ! is_admin() )
		return;
	
		static $instance = 0;
		$instance++;
		
		$defaults = array(
			'id'					=> 'tify_control_media_image-'. $instance,
			'name'					=> 'tify_control_media_image-'. $instance,
			'value'					=> 0,	// Attachment ID
			'default'				=> '',	// Attachment ID | Url de l'image
			'default_color'			=> "#F4F4F4",
			'width' 				=> 1920,
			'height' 				=> 360,
			'size'					=> 'full',
			'inner_html'			=> '',		
			'media_library_title' 	=> __( 'Personnalisation de l\'image', 'tify' ),
			'media_library_button'	=> __( 'Utiliser cette image', 'tify' ),
			'image_editable'		=> true,
			'echo'					=> 1
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );
		
		// Calcul du ratio
		$ratio = 100 * ( $height / $width );
		add_action( 'admin_print_footer_scripts', create_function( '', 'echo "<style type=\"text/css\">#'. $id .':before{padding-top:'. $ratio .'%;}</style>";' ) );
	
		// Traitement de la valeur
		$default 	= ( is_numeric( $default ) && ( $default_image = wp_get_attachment_image_src( $default, $size ) ) ) ? $default_image[0] : ( is_string( $default ) ? $default: '' );
		$value 		= ( is_numeric( $value ) && ( $image = wp_get_attachment_image_src( $value, $size ) ) ) ? $image[0] : ( ( is_string( $value ) && !empty( $value) ) ? $value : $default );
		
		$output  = "";
		$output .= "<div id=\"{$id}\" class=\"tify_control_media_image\" style=\"background-color:{$default_color}; max-width:{$width}px; max-height:{$height}px;\">\n";
		$output .= "\t<a href=\"#tify_control_media_image-add\"".
					" class=\"tify_control_media_image-add\"";
		foreach( $args as $k => $v ) 
			$output .= " data-$k=\"". esc_attr( ${$k} ) ."\"";
		$output .= " title=\"". __( 'Modification de l\'image', 'tify' ) ."\"";
		$output .= " style=\"background-image:url( $value ); ". ( $image_editable ? 'cursor:pointer;' : 'cursor:default;' )."\"";
		$output .= ">\n";
		if( $image_editable )
			$output .= "\t\t<i class=\"tify_control_media_image-add_ico\"></i>\n";
		$output .= "\t</a>\n";	
		$output .= "\t<span class=\"tify_control_media_image-size\">". sprintf( __( '%dpx / %dpx', 'tify' ), $width, $height ) ."</span>\n";
		if( $inner_html )
			$output .= "\t<div class=\"tify_control_media_image-inner_html\">". $inner_html ."</div>\n";
		
		$output .= "\t<input type=\"hidden\" class=\"tify_control_media_image-input\" name=\"{$name}\" value=\"{$args['value']}\" />\n";
		$output .= "\t<a href=\"#tify_control_media_image-reset\" title=\"". __( 'Rétablir l\'image originale', 'tify' ) ."\" class=\"tify_control_media_image-reset tify_button_remove\" style=\"display:". ( ( $value && ( $value != $default ) )? 'inherit' : 'none' ) .";\"></a>";
		$output .= "</div>\n";
		
		if( $echo )
			echo $output;
		else
			return $output;
	}
}

/**
 * Affichage du contrôleur d'image de la médiathèque
 */ 
function tify_control_media_image( $args = array() ){
	global $tiFy_controls;
	
	return $tiFy_controls->media_image->display( $args );
}