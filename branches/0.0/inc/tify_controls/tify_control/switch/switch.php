<?php
/**
 * Bouton de bascule on/off
 * @see http://php.quicoto.com/toggle-switches-using-input-radio-and-css3/
 * @see https://github.com/ghinda/css-toggle-switch
 * @see http://bashooka.com/coding/pure-css-toggle-switches/
 * @see https://proto.io/freebies/onoff/
 */ 
class tiFy_Control_Switch_Class extends tify_control{
	/* = Constructeur = */
	function __construct( $master ){
		$this->dir = dirname( __FILE__ );
		parent::__construct( $master );
	}
		
	/* = Déclaration des scripts = */
	function register_scripts(){
		wp_register_style( 'tify_controls-switch', $this->uri ."/switch.css", array( ), '150310' );
	}
		
	/* = Affichage du controleur = */
	function display( $args = array() ){
		static $instance = 0;
		$instance++;
		
		$defaults = array(
			'id'				=> 'tify_control_switch-'. $instance,
			'class'				=> 'tify_control_switch',
			'name'				=> 'tify_control_switch-'. $instance,
			'label_on'			=> _x( 'Oui', 'tify_control_switch', 'tify' ),
			'label_off'			=> _x( 'Non', 'tify_control_switch', 'tify' ),
			'value_on'			=> 'on',
			'value_off'			=> 'off',
			'checked' 			=> null,
			'default'			=> 1,
			'echo'				=> 1
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );	
		
		if( is_null( $checked ) )
			$checked = $default;
		
		$output  = "";
		$output .= "<div id=\"{$id}\" class=\"{$class}\" data-tify_control=\"switch\">\n";
		$output .= "\t<div class=\"tify_control_switch-wrapper\">\n";
	    $output .= "\t\t<input type=\"radio\" id=\"tify_control_switch-on-{$instance}\" class=\"tify_control_switch-radio tify_control_switch-radio-on\" name=\"{$name}\" value=\"{$value_on}\" autocomplete=\"off\" ". checked( ( $value_on === $checked ), true, false ) .">\n";
	    $output .= "\t\t<label for=\"tify_control_switch-on-{$instance}\" class=\"tify_control_switch-label tify_control_switch-label-on\">{$label_on}</label>\n";
	    $output .= "\t\t<input type=\"radio\" id=\"tify_control_switch-off-{$instance}\" class=\"tify_control_switch-radio tify_control_switch-radio-off\" name=\"{$name}\" value=\"{$value_off}\" autocomplete=\"off\" ". checked( ( $value_off === $checked ), true, false ) .">\n";
	    $output .= "\t\t<label for=\"tify_control_switch-off-{$instance}\" class=\"tify_control_switch-label tify_control_switch-label-off\">{$label_off}</label>\n";
	   	$output .= "\t\t<span class=\"tify_control_switch-selection\"></span>\n";
	  	$output .= "\t</div>\n";
		$output .= "</div>\n";
		
		if( $echo )
			echo $output;
		else
			return $output;
	}
}

/**
 * Affichage du bouton de bascule
 * @param (string) id Attribut id HTML du contrôleur
 * @param (string) class Attribut classe HTML du contrôleur
 * @param (string) name Attribut name du contrôleur, 
 * @param (string) label_on Intitulé de l'élément actif,
 * @param (string) label_off Intitulé de l'élément inactif,
 * @param (string|int) value_on Valeur de l'élément actif,
 * @param (string|int) value_off Valeur de l'élément inactif,
 * @param (string|int) checked Valeur de l'élément selectionné,
 * @param (bool) echo Affichage/Retour du contrôleur
 */
function tify_control_switch( $args = array() ){
	global $tiFy_controls;
	
	return $tiFy_controls->switch->display( $args );
}