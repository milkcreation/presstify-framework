function postToFeed(title, desc, url, image){
var obj = { method: 'feed', link: url, picture: image, name: title, description: desc };
function callback(response){}
	FB.ui(obj, callback);
}

jQuery(document).ready( function($){	
	$('[data-action="tify-fb-api_share_button"]').click(function(e){
		e.preventDefault();
		elem = $(this);
		console.log( elem.data('url') );
		postToFeed( elem.data('title'), elem.data('desc'), elem.data('url'), elem.data('image') );

		return false;
	});
});