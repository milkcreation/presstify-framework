/**
Dependencies: jquery
*/
!( function( $, doc, win, undefined ){
	"use strict";
	var name = 'milk-dynamic-tabs';

	var methods = {
		init: function(opts){
			return this.each(function (i, el) {
				var instance = new MilkDynamicTabs( this, opts );
			});
		}
	};
		
	$.fn.milkDynamicTabs = function( method ){
		if (methods[method]) {
			return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('La methode ' + method + ' n\'existe pas pour jQuery.milkDynamicTabs');
		}
 	};	
		
	function MilkDynamicTabs( el, opts ) {
		this.$el = $(el);
				
		// Mise en cache d'une référence à l'objet 
		this.$el.data(name, this);
	
		// Traitement des options
		var meta  = this.$el.data( name+'-opts' );
		this.defaults = {  
			container : this.$el.data('container'),
			group : this.$el.data('group'),
			name : this.$el.data('name'),
			replace : "#uniqid#",
			callback : function( uniqid ){
	    		return false;
	    	}			
		};
	    this.o = $.extend(true, this.defaults, opts );
		// Initialisation	
	    this.init();	   
	}
	
	// Prototype	
	MilkDynamicTabs.prototype = 
	{
		// Initialisation du plugin	    
	 	init : function( ){
			var self = this;
			// Ecoute des actions sur la galerie
			self._listen();			
		},
		// Ecoute des actions
		_listen : function(){
			var self = this;
			this.$el.click(function(e) {
		        e.preventDefault();
		
		        var id = $(this).parents(".nav-tabs").children().length;
		        var nameid = self._uniqid();
				var container = $(this).data('container');
				var group = $(this).data('group');
				var name = $(this).data('name');
				var reg = new RegExp("#uniqid#", "g");
				var sample_html = $( $(this).data('sample') ).html().replace( reg, nameid );	
						
		        $(this).closest('li').before('<li><a data-toggle="tab" data-current="'+container+',dynamic_tab-content-'+id+'" data-group="'+group+'" href="#dynamic_tab-content-'+id+'"><input type="text" name="'+name+'['+nameid+'][tab-title]" value="Zone de texte #'+id+'"/></a></li>');         
		        $('.tab-content',  $(this).parents(".tab-pane") ).append('<div class="tab-pane" id="dynamic_tab-content-'+id+'">'+sample_html+'</div>').show().each(function() {
					self.o.callback( name, nameid );					     
		        });        
			});		
		},
		_uniqid : function(prefix, more_entropy){
			if (typeof prefix === 'undefined')
				prefix = "";
			
			var retId;
			var formatSeed = function (seed, reqWidth) {
				seed = parseInt(seed, 10).toString(16); // to hex str
				if (reqWidth < seed.length) { // so long we split
					return seed.slice(seed.length - reqWidth);
				}
				if (reqWidth > seed.length) { // so short we pad
					return Array(1 + (reqWidth - seed.length)).join('0') + seed;
				}
				return seed;
			};
			
			if (!this.php_js)
				this.php_js = {};

			if (!this.php_js.uniqidSeed) // init seed with big random int
				this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
			this.php_js.uniqidSeed++;
			
			retId = prefix; // start with prefix, add current milliseconds hex string
			retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
			retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
			if (more_entropy) // for more entropy we add a float lower to 10
				retId += (Math.random() * 10).toFixed(8).toString();
			
			return retId;			
		}	
	};	
})( jQuery, document, window, undefined );