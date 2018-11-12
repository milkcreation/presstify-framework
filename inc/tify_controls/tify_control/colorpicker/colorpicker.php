<?php
class tify_control_colopicker extends tify_control{
	/* = Constructeur = */
	function __construct( $master ){
		$this->dir = dirname( __FILE__ );
		parent::__construct( $master );
	}
		
	/* = Déclaration des scripts = */
	function register_scripts(){
		wp_register_style( 'tify_controls-colorpicker', $this->uri ."/colorpicker.css", array( 'spectrum' ), '141216' );
		$spectrum_depts = array( 'jquery', 'spectrum' );
		if( wp_script_is( 'spectrum-i10n', 'registered' ) )
			$spectrum_depts[] = 'spectrum-i10n';
		wp_register_script( 'tify_controls-colorpicker', $this->uri ."/colorpicker.js", $spectrum_depts, '141216', true );
	}
		
	/* = Affichage du controleur = */
	function display( $args = array() ){
		$defaults = array(				
			'name'				=> '',
			'value' 			=> '',
			'attrs'				=> array(),
			'options'			=> array(), // @see https://bgrins.github.io/spectrum/#options
			'echo'				=> 1
		);	
		$args = wp_parse_args( $args, $defaults );
		extract( $args );
		
		// Traitement des options
		$options = wp_parse_args( $options, array(
		        'preferredFormat' => "hex"
			)
		);
		
		$output = "";
		$output .= "<div class=\"tify_colorpicker\">\n";
		$output .= "<input type=\"hidden\"";
		if( $name )
			$output .= " name=\"$name\"";	
		if( $attrs )
			foreach( $attrs as $iattr => $vattr )
				$output .= " $iattr=\"$vattr\"";		
		if( $options )
			$output .= " data-options=\"". esc_attr( json_encode( $options ) ) ."\"";
		$output .= " value=\"$value\" />";
		$output .= "</div>";
		
		if( $echo )
			echo $output;
		else
			return $output;
	}
}

/**
 * Affichage du selecteur de couleur
 */
function tify_control_colorpicker( $args = array() ){
	global $tiFy_controls;

	return $tiFy_controls->colorpicker->display( $args );
}