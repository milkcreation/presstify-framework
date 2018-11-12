<?php
class tify_control_dropdown_colors extends tify_control{
	/* = Constructeur = */
	function __construct( $master ){
		$this->dir = dirname( __FILE__ );
		parent::__construct( $master );
	}
	
	/* = Déclaration des scripts = */
	function register_scripts(){
		wp_register_style( 'tify_controls-dropdown_colors', $this->uri ."/dropdown_colors.css", array( ), '150512' );
		wp_register_script( 'tify_controls-dropdown_colors', $this->uri ."/dropdown_colors.js", array( 'jquery' ), '150512', true );
	}
		
	/* = Affichage du controleur = */
	function display( $args = array() ){
		static $instance = 0;
		$instance++;
		
		$defaults = array(
			'id'				=> 'tify_control_dropdown_colors-'. $instance,
			'class'				=> 'tify_control_dropdown_colors',
			'name'				=> 'tify_control_dropdown_colors-'. $instance,		
			'selected' 			=> 0,			
			'echo'				=> 1,			
			'color'				=> 'hex',
			'choices'			=> array(),
			'labels'			=> array(),
			'show_label'		=> false,
			'show_option_none' 	=> __( 'Transparent', 'tify' )
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args );
		
		if( $show_label )
			$class = ' show_label';		
		
		$output  = "";
		$output .= "<div id=\"{$id}\" class=\"{$class}\" data-tify_control=\"dropdown_colors\">\n";
		$output .= "\t<span class=\"selected\">\n";
		$selected_value = ( isset( $choices[ $selected ] ) ) ? $choices[$selected] : ( $show_option_none ? $show_option_none : current( $choices ) );
		$selected_label = $show_label ? ( isset( $labels[$selected_value] ) ? $labels[$selected_value] : $show_option_none  ) : '';
		$output .= "\t\t<b>". ( $this->display_value( $selected_value, $selected_label ) ) ."</b>\n";
		$output .= "\t\t<i class=\"caret\"></i>\n";
		$output .= "\t</span>\n";
		$output .= "\t<ul>\n";
		if( $show_option_none ) :
			$output .= "\t\t<li";
			if( ! $selected ) 
				$output .= " class=\"checked\"";
			$output .= ">\n";
	    	$output .= "\t\t\t<label>\n";
			$output .= "\t\t\t\t<input type=\"radio\" name=\"{$name}\" value=\"0\" autocomplete=\"off\" ". checked( ! $selected, true, false ) .">\n";
			$label = $show_label ? $show_option_none : '';
			$output .= $this->display_value( null, $label );
			$output .= "\t\t\t</label>\n";
	    	$output .= "\t\t</li>\n";		
		endif;
		
		foreach( $choices as $value => $color ) :
			$output .= "\t\t<li";
			if( $selected === $value )
				 $output .= " class=\"checked\""; 
			$output .= ">\n";

			$output .= "\t\t\t<label>\n";
			$output .= "\t\t\t\t<input type=\"radio\" name=\"{$name}\" value=\"{$value}\" autocomplete=\"off\" ". checked( $selected  === $value, true, false ) .">\n";
			
			$label = $show_label ? ( isset( $labels[$value] ) ? $labels[$value] : $value ) : '';
			$output .= $this->display_value( $color, $label );
			
			$output .= "\t\t\t</label>\n";

			$output .= "\t\t</li>\n";
		endforeach;
		$output .= "\t</ul>\n";
		$output .= "</div>\n";
		
		if( $echo )
			echo $output;
		else
			return $output;
	}

	function display_value( $color = null, $label = '' ){
		$output  = "";
		$output .= "\t\t\t<div class=\"value\">";
		$output .= "\t\t\t\t<span class=\"color-square". ( $color ? "" : " none" ). "\" style=\"". ( $color ? "background-color:{$color}" : "" ). "\"></span>\n";
		if( $label )			
			$output .= "\t\t\t\t\t<span>{$label}</span>\n";
		$output .= "\t\t\t</div>";
		
		return $output;	
	}
}

/**
 * Affichage de liste déroulante
 */
function tify_control_dropdown_colors( $args = array() ){
	global $tiFy_controls;
	
	return $tiFy_controls->dropdown_colors->display( $args );
}