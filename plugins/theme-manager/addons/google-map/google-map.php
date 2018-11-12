<?php
/*
Addon Name: Google map
Addon URI: http://presstify.com/theme-manager/addons/google-map
Description: Interface de création de carte gmap
Version: 1.0.1
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

/**
 * USAGE :
		Depuis l'éditeur :
			[tify_google-map]
		Directement dans un template :
			<?php echo do_shortcode( '[tify_google-map]' ); ?>
 */

class tiFy_theme_manager_google_map{
	var $tiFy,		
		$dir,
		$uri,
		$path;
		
	/**
	 * Initialisation
	 */
	function __construct(){
		global $tiFy;

		$this->tiFy 	= $tiFy;
		// Définition des chemins
		$this->dir 		= dirname( __FILE__ );
		$this->path  	= $this->tiFy->get_relative_path( $this->dir );
		$this->uri		= $this->tiFy->uri . $this->path;

		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		add_shortcode( 'tify_google-map', array( $this, 'add_shortcode' ) );
	}
	
	/**
	 * ACTIONS ET FILTRES WORPDRESS
	 */
	/**
	 * Initialisation globale
	 */
	function wp_init(){
		wp_register_script( 'tify_google-map', 'https://maps.googleapis.com/maps/api/js?key=&sensor=false&extension=.js', array(), 'v3', false );
	}
	
	/**
	 * Déclaration du shortcode
	 */
	function add_shortcode( $atts = array() ){
		return $this->display( $atts );		
	}
	
	/**
	 * 
	 */
	function display( $args ){
		$defaults = array(
			'address' 		=> '',
			'zoom'			=> 10
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );
		
		// Options d'affichage de la carte
		$map_options 	= json_encode( array(
				'zoom'						=> (int) $zoom,
				'zoomControl'				=> (bool) true,
				'disableDoubleClickZoom'	=> (bool) false,
	            'mapTypeControl'			=> (bool) false,
	            'scaleControl'				=> (bool) false,
	            'scrollwheel'				=> (bool) false,
	            'panControl'				=> (bool) false,
	            'streetViewControl'			=> (bool) false,
	            'draggable' 				=> (bool) true,
	            'overviewMapControl'		=> (bool) true
			), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
		
		$locations		= '';

		// Mise en file des scripts
		wp_enqueue_script( 'tify_google-map' );
		add_action( 'wp_footer', array( $this, 'wp_footer' ), 99 );
		
		return apply_filters( 'tify_google_map_display', "<div id=\"tify_google-map_wrapper\"><div id=\"tify_google-map\" data-address=\"$address\" data-locations='$locations' data-map_options='$map_options'></div></div>", $args, $map_options );
	}
	
	/**
	 * 
	 */
	function wp_footer(){
	?>
	<script type="text/javascript">/* <![CDATA[ */
		jQuery(document).ready( function($){		
			google.maps.event.addDomListener(window, 'load', init);
			var geocoder;
	    	var map;
		    function init() {
		    	var o = $( '#tify_google-map' ).data( 'map_options' );		    	
		        var mapOptions = o;		         
		           /* 
		            zoomControlOptions			: {
		                style	: google.maps.ZoomControlStyle.SMALL,
		            },
		            overviewMapControlOptions	: {
		                opened: false,
		            },
		            mapTypeId					: google.maps.MapTypeId.ROADMAP,*/
		            // @see https://snazzymaps.com
		            //styles						: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#193341"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#2c5a71"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#29768a"},{"lightness":-37}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#406d80"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#406d80"}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#3e606f"},{"weight":2},{"gamma":0.84}]},{"elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"weight":0.6},{"color":"#1a3541"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#2c5a71"}]}]
		       
		        var mapElement 	= document.getElementById('tify_google-map');
		        var map 		= new google.maps.Map(mapElement, mapOptions);
		        var address		= $( '#tify_google-map' ).data( 'address' );
		        var locations	= $( '#tify_google-map' ).data( 'locations' );
		        // Chargement du Geocoder
		        if( address ){
		        	geocoder = new google.maps.Geocoder();
		      	  
			        geocoder.geocode( { 'address': address}, function(results, status) {
				    	if (status == google.maps.GeocoderStatus.OK) {
				     		 map.setCenter(results[0].geometry.location);
				      		var marker = new google.maps.Marker({
					         	map: map,
								position: results[0].geometry.location
							});
				    	}
					});
				} else if( locations ){
					for (i = 0; i < locations.length; i++) {
						
					}
				}
			}		
		});
	/* ]]> */</script>
		<style type="text/css">
		    #tify_google-map {
		        height:350px;
		        width:100%;
		    }
		    .gm-style-iw * {
		        display: block;
		        width: 100%;
		    }
		    .gm-style-iw h4, .gm-style-iw p {
		        margin: 0;
		        padding: 0;
		    }
		    .gm-style-iw a {
		        color: #4272db;
		    }
		    /** Bootstrap Hack **/
		    #tify_google-map img {
				max-width: none;
			}
		</style>
	<?php
	}
}
new tiFy_theme_manager_google_map;