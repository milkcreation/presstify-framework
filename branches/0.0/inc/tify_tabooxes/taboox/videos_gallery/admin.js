/**
Dependencies: jquery
*/
var mkpbx_videos_gallery_src_frame, mkpbx_videos_gallery_poster_frame;
jQuery( document ).ready( function($){
	// Ajout d'une interface de saisie vidéo
	$( '.videos_gallery-add' ).click( function( e ){
		e.preventDefault();
		var name = $(this).data( 'name' );
		var $spinner = $( this ).next( );
		$spinner.show();
		$.post( ajaxurl, { action: 'mkpbx_videos_gallery_add', name: name }, function( resp ){
			$( '#videos_gallery-'+name+'-list' ).append( resp );
			$spinner.hide();
		});
	});
	// Suppression d'une interface de saisie vidéo
	$( document ).on('click', '.videos_gallery-remove', function( e ){
		e.preventDefault();
		$(this).closest( 'li' ).fadeOut(function(){
			$(this).closest( 'li' ).remove();
		});
	});
	// Ajout d'une vidéo de la médiathèque
	$( document ).on('click', '.videos_gallery-media-add', function( e ){
		e.preventDefault();
	 	
	 	var $target = $( this ).closest( 'li' ).find( '.src textarea' );
	 	
		mkpbx_videos_gallery_src_frame = wp.media.frames.file_frame = wp.media({
			title		: $( this ).data( 'media_title' ),
			editing		:    true,
			button		: {
				text : $( this ).data( 'media_button_text' ),
			},
			multiple	: false,
			library		: { type: 'video'} // ['video/flv','video/mp4']}
		});
		 
		mkpbx_videos_gallery_src_frame.on( 'select', function() {
			attachment = mkpbx_videos_gallery_src_frame.state().get('selection').first().toJSON();
			$target.html( attachment.url );
		});	 

		mkpbx_videos_gallery_src_frame.open();
	});	
	// Ajout d'une jaquette pour la vidéo
	$(document).on('click', '.videos_gallery-poster-add', function( e ){
		e.preventDefault();	 	
	 	
	 	var $target = $( this );
	 	
		mkpbx_videos_gallery_poster_frame = wp.media.frames.file_frame = wp.media({
			title 		: $( this ).data( 'media_title' ),
			editing 	:    true,
			button 		: {
				text : $( this ).data( 'media_button_text' ),
			},
			multiple : false,
			library:{ type: 'image'}// ['image/gif','image/png']}
		});
		 
		mkpbx_videos_gallery_poster_frame.on( 'select', function() {
			attachment = mkpbx_videos_gallery_poster_frame.state().get('selection').first().toJSON();
			$target.css( 'background-image', 'url('+attachment.url+'' );
			$target.find( 'input' ).val( attachment.id );
			//$( '#mkpbx-simple_video_poster .reset:hidden' ).fadeIn();
		});	 

		mkpbx_videos_gallery_poster_frame.open();		
	});
	$(document).on('click', '#mkpbx-simple_video_poster .reset', function(e){
		e.preventDefault();
		$(this).fadeOut();
		$( '#mkpbx-simple_video_poster input' ).val( '' );
		$( '#mkpbx-simple_video_poster' ).css( 'background-image', 'none' );		
	});
});
