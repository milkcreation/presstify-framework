jQuery(document).ready( function($){
	// Partage de la page courante
	$( '[data-action="tify-fb-api_share_button"]' ).click( function(e){
		e.preventDefault(); 

		FB.ui( $(this).data( 'params' ));

		return false;
	});
	
	/** @todo **/
	function tify_social_facebook_share_callback( response ) {
		$.post( tify_ajaxurl, { action : 'tify_social_facebook_share_callback', response : response, attrs : callback_attrs }, function( resp ){  });
	};
});