<?php
class tify_control_checkbox extends tify_control{
	/* = Constructeur = */
	function __construct( $master ){
		$this->dir = dirname( __FILE__ );
		parent::__construct( $master );
	}
	
	/* = Déclaration des scripts = */
	function register_scripts(){
		wp_register_style( 'tify_controls-checkbox', $this->uri ."/checkbox.css", array( ), '150420' );
		wp_register_script( 'tify_controls-checkbox', $this->uri ."/checkbox.js", array( 'jquery' ), '150420', true );
	}
		
	/* = Affichage du controleur = */
	function display( $args = array() ){
		static $instance = 0;
		$instance++;
		
		$defaults = array(
			'id'				=> 'tify_control_checkbox-'. $instance,
			'class'				=> 'tify_control_checkbox',
			'name'				=> 'tify_control_checkbox-'. $instance,		
			'value'				=> 0,
			'label'				=> __( 'Aucun','tify_controls' ),
			'label_position'	=> 'R',
			'checked' 			=> 0,			
			'echo'				=> 1
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args );
		
		$output  = "";
		$output .= "<noscript>\n";
		$output .= "\t<style type=\"text/css\">";
		$output .= "\t\t.tify_checkbox{ display:none; }\n";
		$output .= "\t</style>";
		$output .= "\t<div class=\"checkbox\">\n";
		$output .= "\t\t<input type=\"checkbox\" value=\"$value\" name=\"$name\">";
		$output .= "\t\t<label>$label</label>";
		$output .= "\t</div>\n";
		$output .= "</noscript>\n";
		
		$class  .= ( (bool)$checked === true ) ? ' checked' : '';
		
		$output .= "<div id=\"{$id}\" class=\"{$class}\" data-tify_control=\"checkbox\">\n";
		$output .= "\t<label>";
		$output .= "\t\t<ul>\n";
		if( $label_position != 'R' ) :
			// Position du label
			$output .= "\t\t<li class=\"tify_label\">";
			$output .= "\t\t\t<span>$label</span>";
			$output .= "\t\t</li>";
		endif;
		$output .= "\t\t\t<li class=\"tify_checkbox\">";
		$output .= "\t\t\t\t<input type=\"checkbox\" value=\"$value\" name=\"$name\" autocomplete=\"off\" ". checked( (bool)$checked, true, false ) .">";
		$output .= "\t\t\t</li>";
		if( $label_position == 'R' ) :
			// Position du label
			$output .= "\t\t\t<li class=\"tify_label\">";
			$output .= "\t\t\t\t<span>$label</span>";
			$output .= "\t\t\t</li>";
		endif;
		$output .= "\t\t</ul>\n";
		$output .= "\t</label>";
		$output .= "</div>\n";
		
		if( $echo )
			echo $output;
		else
			return $output;
	}
}

/**
 * Affichage de liste déroulante
 */
function tify_control_checkbox( $args = array() ){
	global $tiFy_controls;
	
	return $tiFy_controls->checkbox->display( $args );
}