jQuery( document ).ready( function( $ ){
	/* = ARGUMENTS = */
	/** == Classe == **/
	var action_suffix = $( "#tify_csv-class" ).val();
	/** == Upload == **/
	var files;
	/** ==  Import == **/
	var basename, offset = 0, limit = 0, total = 0;
	
	/* = FICHIER D'EXEMPLE = */
	/*$( '#tify_csv-download_sample' ).click( function(e){
		e.stopPropagation();
    	e.preventDefault();
    	$.post( ajaxurl, { action : 'tify_csv_download_sample_'+ action_suffix }, function( resp ){ });
	});*/
	
	/* = TELECHARGEMENT DU FICHIER D'IMPORT = */
	/** == Déclenchement == **/		
	$( '#tify_csv-uploadfile_button' ).on( 'change', function(e){
		e.stopPropagation();
    	e.preventDefault();
    	var $button = $(this);
    	
    	// Affichage du spinner    	
		$button.next( '.spinner' ).addClass( 'is-active' );
		// Vidage de la table
		$( "#tify_csv-list_table_container" ).empty();
				
    	files = e.target.files;
    	
	    var data = new FormData();
	    $.each( files, function( key, value ){
	        data.append(key, value);
	    });
		    
	    $.ajax({
	        url			: ajaxurl +'?action=tify_csv_upload_'+ action_suffix,
	        type		: 'POST',
	        data		: data,
	        cache		: false,
	        dataType	: 'json',
	        processData	: false,
	        contentType	: false, 
	        success		: function( resp, textStatus, jqXHR ){
	        	// Masquage du spinner
	        	$button.next( '.spinner' ).removeClass( 'is-active' );
	      		$( "#tify_csv-list_table_container" ).html( resp.html );
	      		basename = resp.basename, total = resp.total, offset = resp.offset, limit = resp.limit;
	      		if( total )
	      			$( '#tify_csv-import' ).addClass( 'active' );
	        }
	    });
	});
	
	/* =  IMPORT = */	
	/** == Déclenchement == **/
	$( '#tify_csv-import' ).submit( function(e){
		e.preventDefault();
		if( ! total )
			return;
	   
		tify_csv_import( );
		$( '#tify_progress, #tify_overlay' ).addClass( 'active' );
	});
	function tify_csv_import( ){
		$( '#tify_progress .progress-bar' ).css( 'width', parseInt( ( ( offset/total )*100 ) )+'%' );
		if( offset < total ){
			var options = {};
		    $.each( $( '#tify_csv-import' ).serializeArray(), function( i, j ){
		    	options[j.name] = j.value;
		    });

			$.ajax({
		        url			: ajaxurl,
		        type		: 'POST',
		        data		: { action : 'tify_csv_import_'+ action_suffix, basename : basename, offset : offset, limit : limit, options : options },
		        dataType	: 'json', 
		        success		: function( resp, textStatus, jqXHR ){
		        	$.each( resp, function( u, v ){
		        		if( v !== false  )
		        			$( "#the-list > tr" ).eq( u -1 ).addClass( 'imported' ).find( 'td._tify_csv_result' ).html( '<span class="dashicons dashicons-yes" style="font-size:24px;color:green;"></span>' );
		        	});		        		
		        	offset += limit; 
		        	tify_csv_import( );	        		        	       	
		        }
		    });
		} else {
			basename = undefined, offset = 0, limit = 0, total = 0;
			$( '#tify_csv-import' ).removeClass( 'active' );
    		$( '#tify_progress, #tify_overlay' ).removeClass('active');
		}
	}
});
