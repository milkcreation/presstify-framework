<?php
class tify_control_touch_time extends tify_control{
	/* = Constructeur = */
	function __construct( $master ){
		$this->dir = dirname( __FILE__ );
		parent::__construct( $master );
	}
	
	/* = Déclaration des scripts = */
	function register_scripts(){
		wp_register_style( 'tify_controls-touch_time', $this->uri ."/touch_time.css", array( ), '150418' );
		wp_register_script( 'tify_controls-touch_time', $this->uri ."/touch_time.js", array( 'jquery' ), '150418', true );
	}
	
	/* = Mise en file des scripts = */
	function enqueue_scripts(){
		tify_controls_enqueue( 'dropdown' );
	}
		
	/* = Affichage du controleur = */
	function display( $args = array() ){
		static $instance = 0;
		$instance++;
		
		global $wp_locale;

		$defaults = array(
			'container_id'		=> 'tify_control_touch_time-wrapper-'. $instance,
			'container_class'	=> '',
			'id'				=> 'tify_control_touch_time-'. $instance,
			'name' 				=> 'tify_control_touch_time-'. $instance,
			'value' 			=> false,
			'default' 			=> false,
			'show_none'			=> false, // Permettre les dates de type 0000-00-00 00:00:00 
			'type' 				=> 'datetime', // (default) datetime - ex : 1970-01-01 00:00:00 | date - ex : 1970-00-00 | time - ex 00:00:00 
			'second'			=> false, // Permettre le contrôle des secondes
			'echo' 				=> true,
			'debug'				=> false
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );
		
		switch( $type ) :
			case 'datetime' :
				if( ! $default )
					$default = current_time( 'mysql' ); 
				$event_date	= ( $value && preg_match( '/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $value ) )? $value : $default;
				preg_match( '/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $event_date, $matches );
				$Y = (int) $matches[1]; $m = (int) $matches[2]; $d = (int) $matches[3];
				$H = (int) $matches[4]; $i = (int) $matches[5]; $s = (int) $matches[6];				
				break;
			case 'date' :
				if( ! $default )
					$default = date( 'Y-m-d', current_time( 'timestamp' ) ); 
				$event_date	= ( $value && preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value ) )? $value : $default;
				preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $event_date, $matches );
				$Y = (int) $matches[1]; $m = (int) $matches[2]; $d = (int) $matches[3];
				break;
			case 'time' :
				if( ! $default )
					$default = date( 'H:i:s', current_time( 'timestamp' ) );
				$event_date	= ( $value && preg_match( '/^(\d{2}):(\d{2}):(\d{2})$/', $value ) )? $value : $default;
				preg_match( '/^(\d{2}):(\d{2}):(\d{2})$/', $event_date, $matches );
				$H = (int) $matches[1]; $i = (int) $matches[2]; $s = (int) $matches[3];	
				break;
		endswitch;
		
		// Affichage de la date				
		switch( $type ) :
			case 'datetime' :
			case 'date' :
				/// Affichage de l'année			
				$output_year = "<input type=\"number\" id=\"{$id}-handler-yyyy\" class=\"tify_control_touch_time-handler tify_control_touch_time-handler-yyyy\" value=\"". zeroise( $Y, 4 ) ."\" size=\"4\" maxlength=\"4\" autocomplete=\"off\" />\n";
				/// Affichage du mois
				for ( $i = 1; $i <= 12; $i++ )
					$choices[zeroise( $i, 2 )] = $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) );
				$output_month  = "";
				$output_month .= "<select id=\"{$id}-handler-mm\" class=\"tify_control_touch_time-handler tify_control_touch_time-handler-mm\" autocomplete=\"off\" name=\"\">";
				if( $show_none )
					$output_month .= "<option value=\"0\" ". selected( !$m, true, false ) ."></option>";
				foreach( $choices as $mval => $mlabel )
					$output_month .= "<option value=\"{$mval}\" ". selected( $m == $mval, true, false ) .">{$mlabel}</option>";
				$output_month .= "</select>";
				/// Affichage du jour		
				$output_day = "<input type=\"number\" id=\"{$id}-handler-dd\" class=\"tify_control_touch_time-handler tify_control_touch_time-handler-dd\" value=\"". zeroise( $d, 2 ) ."\" size=\"2\" min=\"". ( $show_none ? 0: 1 ) ."\" max=\"31\" maxlength=\"2\" autocomplete=\"off\" />\n";
				break;
		endswitch;
		
		// Affichage de l'heure
		switch( $type ) :		
			case 'datetime' :
			case 'time' :	 	
				// Affichage de l'heure		
				$output_hour = "<input type=\"number\" id=\"{$id}-handler-hh\" class=\"tify_control_touch_time-handler tify_control_touch_time-handler-hh\" value=\"". zeroise( $H, 2 ) ."\" size=\"2\" maxlength=\"2\" min=\"0\" max=\"23\" autocomplete=\"off\" />\n";
				
				// Affichage des minutes
				$output_minute = " : <input type=\"number\" id=\"{$id}-handler-ii\" class=\"tify_control_touch_time-handler tify_control_touch_time-handler-ii\" value=\"". zeroise( $i, 2 ) ."\" size=\"2\" maxlength=\"2\" min=\"0\" max=\"59\" autocomplete=\"off\" />\n";
				
				// Affichage des secondes
				$output_second = "". ( $second ? ' : ' : '' ) ."<input type=\"". ( $second ? 'number' : 'hidden' ) ."\" id=\"{$id}-handler-ss\" class=\"tify_control_touch_time-handler tify_control_touch_time-handler-ss\" value=\"". zeroise( $s, 2 ) ."\" ". ( $second ? 'size=\"2\" maxlength=\"2\" min=\"0\" max=\"59\" autocomplete=\"off\"' : '' ) ." />\n";		
				break;
		endswitch;
				
		// Sortie
		$output  = "";		
		$output .= "<div id=\"{$container_id}\" class=\"tify_control_touch_time-wrapper". ( $container_class ? ' '.$container_class : '' ) ."\" data-tify_control=\"touch_time\">\n";
		
		/// Réglage de la date
		switch( $type ) :			
			case 'datetime' :
			case 'date' :				
				$output .= "\t<span id=\"{$id}-date\">\n";
				$output .= sprintf( __( '%2$s %1$s %3$s', 'tify' ), $output_month, $output_day, $output_year );
				$output .= "\t\t<i class=\"datepicker-handler dashicons dashicons-calendar-alt\"></i>";
				$output .= "\t</span>";
				break;
		endswitch;
		
		/// Réglage de l'heure
		switch( $type ) :
			case 'datetime' :
			case 'time' :				
				$output .= "\t<span id=\"{$id}-time\">\n";
				$output .= sprintf( __( '%1$s %2$s %3$s', 'tify' ), $output_hour, $output_minute, $output_second );
				$output .= "\t\t<i class=\"timepicker-handler dashicons dashicons-clock\"></i>";
				$output .= "\t</span>\n";
				break;
		endswitch;
		
		$output .= "\t<input type=\"". ( $debug ? 'text' : 'hidden' )."\" value=\"". $value ."\" id=\"{$id}\" class=\"tify_control_touch_time-input\" name=\"{$name}\" autocomplete=\"off\" readonly=\"readonly\">\n";
		$output .= "</div>\n";				
		
		if( $echo )
			echo $output;
		else	
			return $output;
	}
}

/**
 * Affichage de la zone de texte avec décompte de caractères
 */ 
function tify_control_touch_time( $args = array() ){
	global $tiFy_controls;
	
	return $tiFy_controls->touch_time->display( $args );
}