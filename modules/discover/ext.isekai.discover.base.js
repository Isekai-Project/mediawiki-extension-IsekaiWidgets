$(function(){
    if($('.isekai-discover').length > 0){
		var Discover = isekai.Discover;
		$('.isekai-discover').each(function(){
			new Discover($(this));
		});
	}
});