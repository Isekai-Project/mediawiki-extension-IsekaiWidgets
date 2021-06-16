$(function(){
    if($('.isekai-create-page-panel').length > 0){
		var CreatePagePanel = isekai.CreatePagePanel;
		$('.isekai-create-page-panel').each(function(){
			new CreatePagePanel($(this));
		});
	}
});