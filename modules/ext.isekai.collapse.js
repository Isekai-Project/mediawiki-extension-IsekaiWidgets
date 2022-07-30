(function($) {
    $('.isekai-collapse').addClass('animate')
    $('.isekai-collapse .isekai-collapse-title').on('click', '', function(e) {
        e.preventDefault();
        var titleElem = $(this);
        var containerElem = titleElem.parent('.isekai-collapse');
        var contentElem = containerElem.find('.isekai-collapse-content');
        if (containerElem.prop('open')) { // 需要收起
            var collapsedHeight = titleElem.outerHeight();
            var expandedHeight = collapsedHeight + contentElem.outerHeight();
            containerElem.css('height', expandedHeight);
            console.log('expandedHeight', expandedHeight);
            requestAnimationFrame(function() {
                console.log('collapsedHeight', collapsedHeight);
                containerElem.addClass('closing').css('height', collapsedHeight);
                setTimeout(function() {
                    containerElem.prop('open', false).removeClass('closing'); //.css('height', 'auto');
                }, 260);
            });
        } else { // 需要展开
            containerElem.prop('open', true);
            var collapsedHeight = titleElem.outerHeight();
            containerElem.css('height', collapsedHeight);
            requestAnimationFrame(function() {
                var expandedHeight = collapsedHeight + contentElem.outerHeight();
                containerElem.css('height', expandedHeight);
                /*setTimeout(function() {
                    containerElem.css('height', 'auto');
                }, 260);*/
            });
        }
    });
})(jQuery);
