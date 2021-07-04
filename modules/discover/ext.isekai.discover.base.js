$(function(){
    if($('.isekai-discover').length > 0){
		var DiscoverWidget = isekai.ui.DiscoverWidget;
		$('.isekai-discover').each(function(){
			new DiscoverWidget($(this));
		});
	}
});