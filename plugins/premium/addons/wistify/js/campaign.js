jQuery(document).ready( function($){
	/* = ARGUMENTS = */
	var campaign_id = 0, 	// Identifiant de la campagne
		total = 0;			// Nombre total d'abonnés
			
	/* = ELEMENTS DU DOM = */	
	var	$progress = $( '#progress-infos .progress .progress-bar' ),
		$indicator = $( '#progress-infos .progress .indicator' );
	
	/* = LISTE = */
	/** == Reprise de la préparation == **/
	$( 'a[data-resume="preparing"]').click( function(e){
		e.preventDefault();
		campaign_id = $(this).data( 'campaign_id' );
		
		// Ouverture Fenêtre d'infos		
		$( 'body' ).append( '<div id="wistify_campaign-overlay"></div>' );
		$( '#progress-infos' ).fadeIn();
		
		wistify_campaign_prepare();
	});
	
	/** == Reprise de l'acheminement == **/
	$( 'a[data-resume="in-progress"]').click( function(e){
		e.preventDefault();
		campaign_id = $(this).data( 'campaign_id' );
		var token =  $(this).data( 'token');
		
		// Ouverture Fenêtre d'infos		
		$( 'body' ).append( '<div id="wistify_campaign-overlay"></div>' );
		$( '.processed', $indicator ).html( token.processed +' '+ wistify_campaign.emails_sent +' ' );
		$( '.total', $indicator ).html( ' '+ token.total );
		$progress.css( 'width', parseInt( ( ( token.processed/token.total )*100 ) )+'%'  );
		$( '#progress-infos' ).fadeIn();
		
		wistify_campaign_send();
	});
	
	/* = EDITION = */
	/** == TEST DE LA CAMPAGNE == */
	$( '#send-test-submit > button' ).click( function(e){
		e.preventDefault();
		$closest = $(this).closest('div');
	
		$.post( 
			ajaxurl, 
			{ 
				action 			: 'wistify_messages_send',
				_wty_ajax_nonce : $( '#_wty_messages_send_ajax_nonce', $closest ).val(),
				campaign_id		: $( '#campaign_id' ).val(),
				service_account : $( '#wistify_messages_send_service_account', $closest ).val(),
				recipient_email	: $( '#wistify_messages_send_to_email', $closest ).val(),
				message 		: {
					subject : $( '#wistify_messages_send_subject', $closest ).val()											
				}
			}, 
			function( resp ){
				$.each( resp[0], function(u,v){					
					if( v )
						$( '#send-test-resp > .'+u ).html( v );
				});				
			},
			'json'
		);
	});
	
	/** == ETAPE #3 - Choix des destinataires == **/
	/*** === Recherche par autocompletion === ***/
	var name,
		type, 
		list = '#recipients-list', 
		total = 0;
	 
	$( '#recipient-search' ).autocomplete({
		appendTo: "#recipient-search-results",
		source:	function( request, response ){
			$.post(	ajaxurl, { action : 'wistify_search_autocomplete_recipients', term : request.term, name : name, type : type }, function( data ){
				response(data);
			}, 'json' );
		},
		search: function( event, ui ) {					
			name 	= $( this ).data( 'name' );
			type 	= $( this ).data( 'type' );
		},
		minLength:	2,
		select: function( event, ui ) {			
			event.preventDefault();
			var item = ui.item;
			$( list ).append( item.value );
			$(this).val('');
			update_recipients_total();			
		}
	});
	if( $( '#recipient-search' ).size() )
		$( '#recipient-search' ).each( function(){
			$(this).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" )
				.append( item.label )
				.appendTo( ul );
			};
		});
	/** == Suppression d'un items == **/
	$(document).on('click', list +'> li > .remove', function(e){
		e.preventDefault();	
		$(this).parent().fadeOut( function(){
			$(this).remove();
			update_recipients_total();
		});
	});
	/** ==  Mise à jour du nombre de destinataires == **/
	function update_recipients_total(){
		total = 0;
		$( list +' > li' ).each( function(){
			total += parseInt( $(this).data( 'numbers') );
		});
		$( '#recipients-total > .value' ).html( total );
	}
	
	/** == Préparation de l'envoi == **/
	/*** === Déclaration des variables === ***/
	var	recipients = [],	// Abonnements à traiter
		types = [],			// Liste des types d'abonnement à traiter
		count = [],			// Nombre d'abonnés par type 	 
		processed = 0,		// Nombre d'abonné traités 
		per_page = 250,		// Nombre d'abonné par passe 
		paged = 1,			// Passe courante 
		type = 0,			// Type d'abonnement en cours de traitement
		list_id = 0, list_index = 0;			
	
	/*** === Lancement de la préparation de la campagne === ***/			
	$( '#campaign-send' ).click( function(e){
		e.preventDefault();
		
		campaign_id = $( 'input#campaign_id' ).val();
		// Ouverture Fenêtre d'infos		
		$( 'body' ).append( '<div id="wistify_campaign-overlay"></div>' );
		$progress.css( 'width', 0 );
		$( '#progress-infos > h3 ' ).html( wistify_campaign.preparing );
		$( '#progress-infos' ).fadeIn();
				
		wistify_campaign_prepare();
	});
	
	/*** === Lancement de la préparation de la campagne === ***/
	function wistify_campaign_prepare(){
		$.ajax({
			url 		: ajaxurl,
			data 		: { action : 'wistify_campaign_prepare', campaign_id : campaign_id }, 
			success		: function( resp ){
				recipients = resp.recipients;
				types = resp.types;
				count = resp.count;
				total = resp.total;
				
				$( '.total', $indicator ).html( ' '+ total );
			
				wistify_campaign_prepare_recipients();				
			},
			dataType	: 'json'			
		});
	}
	
	/*** === Mise en file des destinataires en base === ***/
	function wistify_campaign_prepare_recipients(){
		if( processed < total ){
			$progress.css( 'width', parseInt( ( ( processed/total )*100 ) )+'%' );
			$( '.processed', $indicator ).html( processed +' '+ wistify_campaign.emails_ready +' ' );			
			switch( types[type]){
				case 'wystify_subscriber' :
					var start =  (paged-1)*per_page, end = start+per_page,					
						subscriber_ids = recipients[types[type]].slice( start, end );
					
					if( subscriber_ids.length ){
						$.ajax({
							url 		: ajaxurl,
							data 		: { 
								action 			: 'wistify_campaign_prepare_recipients_subscriber', 
								campaign_id 	: campaign_id,
								subscriber_ids	: subscriber_ids				
							}, 
							success		: function( resp ){
								processed += resp.total;
								if( ! resp.total ){
									paged = 1;
									type ++;
								} else {
									paged ++;
								}					
								wistify_campaign_prepare_recipients();
							},
							dataType	: 'json'			
						});
					} else {
						paged = 1;
						type ++;
						wistify_campaign_prepare_recipients();
					}					
					break;
				case 'wystify_mailing_list' :
					var list_id = recipients[types[type]][list_index];
					if( list_id ){						
						$.ajax({
							url 		: ajaxurl,
							data 		: { 
								action 		: 'wistify_campaign_prepare_recipients_mailing_list', 
								campaign_id : campaign_id,
								list_id		: list_id,
								paged 		: paged,
								per_page 	: per_page					
							}, 
							success		: function( resp ){
								processed += resp.total;
								if( ! resp.total ){
									paged = 1;
									list_index++;
									if( ! recipients[types[type]][list_index] )
										type ++;
								} else {
									paged ++;
								}					
								wistify_campaign_prepare_recipients();
							},
							dataType	: 'json'			
						});
					} else {
						paged = 1;
						type ++;
						wistify_campaign_prepare_recipients();
					}	
					break;
			}	
		} else {			
			$progress.css( 'width', '100%' );
			$( '.processed', $indicator ).html( processed +' '+ wistify_campaign.emails_ready +' ' );
			$( '#progress-infos > h3 ' ).html( wistify_campaign.sending );
			$progress.css( 'width', 0 );
			
			// Remise à zéro des arguments
			recipients = [],	// Abonnements à traiter
			types = [],			// Liste des types d'abonnement à traiter
			count = [],			// Nombre d'abonnés par type 	 
			processed = 0,		// Nombre d'abonné traités 
			per_page = 250,		// Nombre d'abonné par passe 
			paged = 1,			// Passe courante 
			type = 0,			// Type d'abonnement en cours de traitement
			list_id = 0, list_index = 0;			
			
			wistify_campaign_send();					
		}	
	}
		
	/*** === Lancement de l'envoi de la campagne === ***/			
	function wistify_campaign_send(){
		$.ajax({
			url 		: ajaxurl,
			data 		: { action : 'wistify_campaign_send_emails', campaign_id : campaign_id }, 
			success		: function( resp ){
				if( resp ){
					$progress.css( 'width', parseInt( ( ( resp.processed/resp.total )*100 ) )+'%' );
					$( '.processed', $indicator ).html( resp.processed +' '+ wistify_campaign.emails_sent +' ' );
					wistify_campaign_send();
				} else {
					campaign_id = 0, 	// Identifiant de la campagne					
					total = 0,			// Nombre total d'abonnés
					
					$( '#progress-infos' ).fadeOut();
					$( '#wistify_campaign-overlay' ).fadeOut( function(){
						$(this).remove();
					});
				}				
			},
			dataType	: 'json'			
		});
	}	
});