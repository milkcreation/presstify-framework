<?php
namespace PresstiFy\Set\Animations;

/**
 * Usage :
 * 		1) Pour les animations au scroll :
 * 			- data-animate-scroll-target="#IdentifiantDeLaCible" sur le tag html concerné par l'animation si le déclenchement 
 * 			  de l'animation doit avoir lieu lors de la détection de la cible dans la zone visible de la fenêtre.
 * 			- data-animate-scroll-position="PositionEnPixels" (ex: 300) sur le tag html concerné par l'animation si le déclenchement 
 * 			  de l'animation doit avoir lieu à une position de scroll précise.
 * 			- Si aucun des 2 précédents paramètres n'est configuré, l'animation se déclenche lors de la détection de l'élément concerné
 * 			  dans la zone visible de la fenêtre.
 * 
 * 		1) Animations tiFy :
 * 			  - Ajouter les classes sur le tag html concerné par l'animation (exemple : tiFy-animate + tiFy-animate--hover + tiFy-scaleUp) 
 * 				produira un effet de grossissement de l'élément d'une durée d'une seconde au survol de celui-ci.
 * 			  - Si l'animation se passe au scroll, ajouter la classe "tiFy-animate--scroll".
 * 
 * 		2) Animations Animate.css :
 * 				@see https://github.com/daneden/animate.css
 * 			  - Ajouter les classes sur le tag html concerné par l'animation.
 * 			  - Si l'animation se passe au scroll, ajouter la classe "animateCSS-scroll" et déclarer sur le tag concerné l'attribut
 * 				data-scroll-animation="ClasseDeLAnimation".
 * 
 * Credits
 * @see https://github.com/IanLunn/Hover
 * @see https://github.com/daneden/animate.css
 */

class Animations extends \tiFy\App\Factory
{
	/* = ARGUMENTS = */
	// Liste des Actions à déclencher
    protected $tFyAppActions                = array(
        'init',
        'theme_before_enqueue_scripts'
    );

    // Fonctions de rappel des actions
    protected $tFyAppActionsMethods    = array(
        'init'                              => 'register_scripts',
        'theme_before_enqueue_scripts'      => 'enqueue_scripts'        
    );
    
	/* = DECLENCHEURS = */
	/** == Déclaration des scripts == **/
	final public function register_scripts()
	{
	    // Animate CSS   
	    tify_register_style(
           'animateCSS',
	        array(
    			'src'		=> array(
    				'local'		=> static::getUrl( get_class() ) .'/vendor/animate.css-master/source/animate.min.css',
    				'cdn'		=> '//cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css'
    			),
    			'deps'		=> array(),
    			'version'	=> '3.5.2',
    			'media'		=> 'all' 
    		)
        );
        
        // Hover CSS
		tify_register_style(
			'hoverCSS',
		    array(
				'src'		=> array(
					'local'		=> static::getUrl( get_class() ) .'/vendor/Hover-master/css/hover-min.css',
				),
				'deps'		=> array(),
				'version'	=> '2.1.0',
				'media'		=> 'all'
			)
        );
		
		// Déclaration des scripts
        wp_register_style( 'tiFyCollectionAnimations',    static::getUrl( get_class() ) ."/Animations.css",  array( 'animateCSS', 'hoverCSS' ), '170112' );
		wp_register_script( 'tiFyCollectionAnimations',   static::getUrl( get_class() ) ."/Animations.js",   array( 'jquery' ), '170112', true );	
		
		$output = "";
		foreach( range( 0, 5000, 100 ) as $time ) :
			$output .= ".tiFy-animateDuration--{$time}ms{-webkit-animation-duration:{$time}ms;animation-duration:{$time}ms;-webkit-transition-duration:{$time}ms;transition-duration:{$time}ms;} .tiFy-animateDelay--{$time}ms{-webkit-animation-delay:{$time}ms;animation-delay:{$time}ms;-webkit-transition-delay:{$time}ms;transition-delay:{$time}ms;}";		
		endforeach;
		
		wp_add_inline_style( 
			'tiFyCollectionAnimations', 
			$output
		);
	}	
	
	/** == Mise en file des scripts de l'interface utilisateur == **/
	public function enqueue_scripts()
	{
	    wp_enqueue_style( 'tiFyCollectionAnimations' );
		wp_enqueue_script( 'tiFyCollectionAnimations' );		
	}
}