<?php
/*
Plugin Name: Google map
Plugin URI: http://presstify.com/plugins/google-map
Description: Interface de création de carte gmap
Version: 1.1.322
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

/**
 * USAGE :
Recommandé :
Charger les dépendance de scripts :
wp_enqueue_style( 'tiFy_GoogleMap' );
wp_enqueue_script( 'tiFy_GoogleMap' );

Depuis l'éditeur tinyMCE Wordpress :
	[tiFy_GoogleMap]
	
Dans un template :
	<?php echo do_shortcode( '[tiFy_GoogleMap]' ); ?>
 */

namespace tiFy\Plugins\GoogleMap; 

class GoogleMap extends \tiFy\Environment\Plugin
{
	/* = ARGUMENTS = */
	// Liste des actions à déclencher
	protected $tFyAppActions				= array(
		'init'
	);
				
	/* = DECLENCHEURS = */
	/** == Initialisation globale == */
	final public function init()
	{
		// Déclaration du shortcode		
		add_shortcode( 'tiFy_GoogleMap', array( $this, 'add_shortcode' ) );
		
		// Déclaration
	    wp_register_style( 'tiFy_GoogleMap', self::tFyAppUrl().'/GoogleMap.css', array(), 'v3', false );
		wp_register_script( 'tiFy_GoogleMap', '//maps.googleapis.com/maps/api/js?key='. self::tFyAppConfig('ApiKey'), array(), 'v3', false );
	}
	
	/** == Déclaration du shortcode == **/
	final public function add_shortcode( $atts = array() )
	{
		return self::display();		
	}
	
	/* = CONTROLEURS = */
	/** == Affichage de la Google Map == **/
	final public static function display()
	{		
		// Mise en file des scripts
		if( ! wp_style_is( 'tiFy_GoogleMap' ) )
	       wp_enqueue_style( 'tiFy_GoogleMap' );
	       
		if( ! wp_script_is( 'tiFy_GoogleMap' ) )
	       wp_enqueue_script( 'tiFy_GoogleMap' );
		
		$wp_footer = function()
		{
			$MapOptions 		= wp_parse_args( self::tFyAppConfig( 'MapOptions' ), self::tFyAppConfigDefault( 'MapOptions' ) );
			$litteralMapOpts	= array( 
				'fullscreenControlOptions', 'mapTypeControlOptions', 'panControlOptions', 
				'rotateControlOptions', 'scaleControlOptions', 'streetViewControlOptions', 
				'styles', 'zoomControlOptions' 
			);
			$MarkerOptions		= self::tFyAppConfig( 'MarkerOptions' );
			$litteralMarkerOpts = array();
				
		?><script type="text/javascript">/* <![CDATA[ */
			google.maps.event.addDomListener( window, 'load', tiFyGoogleMapInit );
				
			function tiFyGoogleMapInit() {
				// INITIALISATION DE LA CARTE				
				/// Options de la carte
				var mapOptions = {
					<?php foreach( (array) $MapOptions as $k => $v ) : ?>		
						<?php echo $k;?>: <?php echo ! in_array( $k, $litteralMapOpts ) ? json_encode($v) : /* mode littéral */ $v;?>,
					<?php endforeach;?>
				};

				/// Conteneur d'accroche de la carte						
				var mapElement = document.getElementById( 'tiFy_GoogleMap' );
				
				/// Instanciation de la carte
				var map = new google.maps.Map( mapElement, mapOptions );
				
				// INITIALISATION DU MARQUEUR PRINCIPAL
				/// Options du marqueur
				var markerOptions = {
					<?php foreach( (array) $MarkerOptions as $i => $j ) : ?>		
						<?php echo $i;?>: <?php echo ! in_array( $i, $litteralMarkerOpts ) ? json_encode($j) : /* mode littéral */ $j;?>,
					<?php endforeach;?>
				};
				markerOptions.map = map;				
				
				/// Positionnement du marqueur selon son adresse
				if( address = "<?php echo self::tFyAppConfig( 'Address' );?>" ){
		        	geocoder = new google.maps.Geocoder();
		      	  
			        geocoder.geocode( { 'address': address }, function(results, status) {
				    	if( status == google.maps.GeocoderStatus.OK ) {
				     		map.setCenter( results[0].geometry.location );
				     		markerOptions.position = results[0].geometry.location
				  			var marker = new google.maps.Marker( markerOptions );
				    	}
					});
				/// Positionnement du marqueur selon sa lattitude et longitude	
				} else {
					var marker = new google.maps.Marker( markerOptions );
				}

				// Responsive
				google.maps.event.addDomListener(window, "resize", function() {
				   var center = map.getCenter();
				   google.maps.event.trigger(map, "resize");
				   map.setCenter(center); 
				});
	      	}
		/* ]]> */</script><?php 	
		};		
		add_action( 'wp_footer', $wp_footer, 25 );
		
		return "<div id=\"tiFy_GoogleMap\"></div>";
	}
}
