$(function(){
    if($('.isekai-create-page').length > 0){
		var CreatePageWidget = isekai.ui.CreatePageWidget;
		$('.isekai-create-page').each(function(){
			new CreatePageWidget($(this));
		});
	}
});