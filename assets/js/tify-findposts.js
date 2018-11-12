/**
 * @name : Findpost
 * @description : Pop-in de récupération de post
 * @usage :
 * 1 - Appeler le script :
	wp_enqueue_script( 'tify-findposts' );
 * 
 * 2 - Ajouter au lien d'appel de la modale l'instruction : 
	onclick=\"findPosts.open( 'target', '[id de la cible]' ); return false;" // 'target', '[id de la cible]' optionnels, utile par exemple viser un élément du DOM au moment de la soumission 
 * 
 * 3 - A mettre dans la page html :
	<div id="ajax-response"></div>
	<?php find_posts_div();?> 
 * 
 * 4 - Personnalisation de l'action à la soumission depuis la modale (exemple) :
 	$('#find-posts-submit').click( function(e) {
		e.preventDefault();
		var $checked = $('#find-posts-response .found-posts .found-radio input:checked');
		
		if( $checked.size() )
			$.post( ajaxurl, { action : [ajax-action], post_id : $checked.val() }, function( resp ){
				var target; // Récupération de l'[id de la cible]
				if( target = $( '#affected[name="target"]').val() )
					$( target ).val( resp );					
				findPosts.close();
			});
		else 
			findPosts.close();
	});
 * 
 * 
 */

var findPosts;
( function( $ ){
	findPosts = {
		open: function( af_name, af_val ) {
			var overlay = $( '.ui-find-overlay' );

			if ( overlay.length === 0 ) {
				$( 'body' ).append( '<div class="ui-find-overlay"></div>' );
				findPosts.overlay();
			}

			overlay.show();

			if ( af_name && af_val ) {
				$( '#affected' ).attr( 'name', af_name ).val( af_val );
			}

			$( '#find-posts' ).show();

			$('#find-posts-input').focus().keyup( function( event ){
				if ( event.which == 27 ) {
					findPosts.close();
				} // close on Escape
			});

			// Pull some results up by default
			findPosts.send();

			return false;
		},

		close: function() {
			$('#find-posts-response').html('');
			$('#find-posts').hide();
			$( '.ui-find-overlay' ).hide();
		},

		overlay: function() {
			$( '.ui-find-overlay' ).on( 'click', function () {
				findPosts.close();
			});
		},

		send: function() {
			var post = {
					ps: $( '#find-posts-input' ).val(),
					action: 'find_posts',
					_ajax_nonce: $('#_ajax_nonce').val()
				},
				spinner = $( '.find-box-search .spinner' );

			spinner.show();

			$.ajax( ajaxurl, {
				type: 'POST',
				data: post,
				dataType: 'json'
			}).always( function() {
				spinner.hide();
			}).done( function( x ) {
				if ( ! x.success ) {
					$( '#find-posts-response' ).text( attachMediaBoxL10n.error );
				}

				$( '#find-posts-response' ).html( x.data );
			}).fail( function() {
				$( '#find-posts-response' ).text( attachMediaBoxL10n.error );
			});
		}
	};

	$( document ).ready( function() {
		$( '#find-posts-submit' ).click( function( event ) {
			if ( ! $( '#find-posts-response input[type="radio"]:checked' ).length )
				event.preventDefault();
		});
		$( '#find-posts .find-box-search :input' ).keypress( function( event ) {
			if ( 13 == event.which ) {
				findPosts.send();
				return false;
			}
		});
		$( '#find-posts-search' ).click( findPosts.send );
		$( '#find-posts-close' ).click( findPosts.close );
		$( '#doaction, #doaction2' ).click( function( event ) {
			$( 'select[name^="action"]' ).each( function() {
				if ( $(this).val() === 'attach' ) {
					event.preventDefault();
					findPosts.open();
				}
			});
		});

		// Enable whole row to be clicked
		$( '.find-box-inside' ).on( 'click', 'tr', function() {
			$( this ).find( '.found-radio input' ).prop( 'checked', true );
		});
	});
})( jQuery );
