/**
Dependencies: jquery, jquery-ui-sortable
Veersion: 2.0
*/

var mkpbx_images_gallery_frame;

jQuery(document).ready( function($){
	// Affichage du selecteur de média
	$(document).on('click', '.images_gallery-add', function( e ){
		e.preventDefault();
	 	
	 	var name = $(this).data('name');
		var $target = $(this).prev();
		
		mkpbx_images_gallery_frame = wp.media.frames.file_frame = wp.media({
			title: $( this ).data( 'media_title' ),
			editing:    true,
			button: {
				text: $( this ).data( 'media_button_text' ),
			},
			multiple: true,
			library:{ type: 'image'}// ['image/gif','image/png']}
		});
		 
		mkpbx_images_gallery_frame.on( 'select', function() {
			var selection = mkpbx_images_gallery_frame.state().get('selection');
			selection.map( function( attachment ) {
				var order = $( 'li', $target ).size()+1;
			 	attachment = attachment.toJSON();
			 	html  = '<li>';
			 	html += '<img src="'+attachment.sizes['thumbnail'].url+'" />';
			 	html += '<input type="hidden" name="mkpbx_postbox[single]['+name+'][]" value="'+attachment.id+'" />';
			 	html += '<a href="#remove" class="tify_button_remove"></a>';
			 	html += '<input type="text" class="order" value="'+order+'" size="1" readonly/>';
			 	html += '</li>';			 	
			 	
				$target.append(html);
			});
		});	
		mkpbx_images_gallery_frame.open();
	});
	// Ordonnacement des images de la galerie
	$( ".images-gallery-list" ).sortable({
		placeholder: "ui-sortable-placeholder",
		update : function( event, ui ){
			$('input.order',  $(this) ).each( function( u, v ){
				$(this).val( $(this).parent().index()+1 );
			});
		}
	});
	$( ".images-gallery-list" ).disableSelection();
	// Suppression d'une image de la galerie
	$( document ).on( 'click', '.images-gallery-list li .tify_button_remove', function(e){
		$container = $(this).parent();
		$container.fadeOut( function(){
			$container.remove();
		});
	});
});
