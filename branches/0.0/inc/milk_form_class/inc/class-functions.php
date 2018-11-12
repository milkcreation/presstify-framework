<?php
class MKCF_Functions{
	var	$options;
	
	public function __construct(MKCF $master) {
        $this->mkcf = $master;
    }
	
	/**
	 * Traitement des options
	 */
	public function parse_options( $options, $defaults ){		
		$_options = array();
		if( ! $options ) :
			$_options = $defaults;
		elseif( ! $defaults ) :
			$_options = $options;
		elseif( is_array( $options ) ) :	
			foreach( (array) $defaults as $key => $default ) :
				if( ! is_array( $default ) ) :
					if( isset( $options[ $key ] ) ) :
						$_options[$key] =  $options[ $key ];
					else : 						
						$_options[$key] = $default;
					endif;
				else :
					if( isset( $options[ $key ] ) && is_array(  $options[ $key ] ) ) :
						$_options[$key] = $this->parse_options( $options[ $key ], $default );
					elseif( isset( $options[ $key ] ) )	:				
						$_options[$key] = $options[ $key ];
					else :
						$_options[$key] = $default;
					endif;
				endif;
				// Nettoyage
				if( isset( $options[ $key ] ) && is_array( $options[ $key ] ) )
					unset( $options[ $key ] );
				unset( $defaults[ $key ] );
			endforeach;
			$_options += $options;
		endif;
		
		return $_options;			
	}
	
	/**
	 * 
	 */
	public function translate_field_value( $subject, $fields, $default = '' ){
		// Bypass 
		if( ! is_string( $subject ) )
			return $default; 
		if( ! preg_match_all( '/%%(.*?)%%([^%%]*)?/', $subject, $matches ) ) // regex plus simple : '/([^\%\%]+)*/'
			return $default;
		if( ! is_array( $matches[1] ) )
			return $default;
		
		$output = "";
		foreach( $matches[1] as $i => $match ) : 
			if( isset( $fields[$match]['value'] ) ) :
				$output .= $fields[$match]['value'];
			endif;
			if( isset( $matches[2][$i] ) ) :
				$output .= $matches[2][$i];
			endif;
		endforeach;			
		
		if( ! $output )
			return $default; 
		else
			return $output; 
	}
}