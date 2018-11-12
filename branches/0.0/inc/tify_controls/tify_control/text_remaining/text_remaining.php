<?php
/**
 * Zone de texte limitée
 * 
 * @see http://www.w3schools.com/tags/tag_textarea.asp -> attributs possibles pour le selecteur textarea
 * @see http://www.w3schools.com/jsref/dom_obj_text.asp -> attributs possibles pour le selecteur input
 */
class tify_control_text_remaining extends tify_control{
	/* = Constructeur = */
	function __construct( $master ){
		$this->dir = dirname( __FILE__ );
		parent::__construct( $master );
	}
	
	/* = Déclaration des scripts = */
	function register_scripts(){
		wp_register_style( 'tify_controls-text_remaining', $this->uri ."/text_remaining.css", array( ), '141213' );
		wp_register_script( 'tify_controls-text_remaining', $this->uri ."/text_remaining.js", array( 'jquery' ), '141213', true );
		wp_localize_script( 'tify_controls-text_remaining', 'tifyTextRemaining', 
			array( 
				'plural' => __( 'caractères restants', 'tify' ), 
				'singular' => __( 'caractère restant', 'tify' ), 
				'none' => __( 'Aucun caractère restant', 'tify' ) 
			) 
		);
	}
		
	/* = Affichage du controleur = */
	function display( $args = array() ){
		static $instance = 0;
		$instance++;
		
		$defaults = array(
			'container_id'		=> 'tify_control_text_remaining-container-'. $instance,
			'id'				=> 'tify_control_text_remaining-'. $instance,
			'feedback_area'		=> '#tify_control_text_remaining-feedback-'. $instance,
			'name'				=> 'tify_control_text_remaining-'. $instance,
			'selector'			=> 'textarea',	// textarea (default) // @TODO | input 
			'value' 			=> '',		
			'attrs'				=> array(),
			'length'			=> 150,	
			'maxlength'			=> true, 	// Stop la saisie en cas de dépassement
			'echo'				=> 1
		);	
		$args = wp_parse_args( $args, $defaults );
		extract( $args );
		
		$output = "";
		switch( $selector ) :
			default :
			case 'textarea' :
					$output .= "<div id=\"{$container_id}\" class=\"tify_control_text_remaining-container\" >\n";
					$output .= "\t<textarea id=\"{$id}\" data-tify_control=\"text_remaining\" data-feedback_area=\"{$feedback_area}\"";
					if( $name )
						$output .= " name=\"{$name}\"";
					if( $maxlength )
						$output .= " maxlength=\"{$length}\"";
					if( $attrs )
						foreach( $attrs as $iattr => $vattr )
							$output .= " {$iattr}=\"{$vattr}\"";
					$output .= ">{$value}</textarea>\n";
					$output .= "\t<span id=\"tify_control_text_remaining-feedback-{$instance}\" class=\"feedback_area\" data-max-length=\"{$length}\" data-length=\"". strlen( $value ) ."\"></span>\n";
					$output .= "</div>\n";
				break;
		endswitch;
		
		if( $echo )
			echo $output;
		else
			return $output;
	}
}

/**
 * Affichage de la zone de texte avec décompte de caractères
 */ 
function tify_control_text_remaining( $args = array() ){
	global $tiFy_controls;
	
	return $tiFy_controls->text_remaining->display( $args );
}