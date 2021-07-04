$(function(){
    var cardList = [];
    $('.isekai-preview-card').each(function(){
        //点击动画
        var animating = false;
        var mouseUp = false;
        var cardElem = $(this);
        cardElem.on('mousedown', function(){
            cardElem.addClass('mousedown');
            mouseUp = false;
            animating = true;
            setTimeout(() => {
                if(mouseUp){
                    cardElem.removeClass('mousedown');
                }
                animating = false;
            }, 150);
        }).on('mouseup', function(){
            if(animating){
                mouseUp = true; 
            } else {
                cardElem.removeClass('mousedown');
            }
        });
        //获取页面列表
        var pageTitle = cardElem.attr('data-title');
        if(pageTitle){
            cardList.push({
                title: pageTitle,
                element: cardElem,
            });
        }
    });

    //加载页面信息
    var titleList = [];
    var pageInfoList = {};
    cardList.forEach((item) => {
        var title = item.title;
        if(titleList.indexOf(title) === -1){
            titleList.push(title);
        }
    });
    var api = new mw.Api();

    function setPreviews(pageInfoList){
        cardList.forEach((item) => {
            var title = item.title;
            var elem = item.element;
            //移除加载动画
            elem.find('.loading').remove();
            //查找数据
            if(title in pageInfoList){
                var info = pageInfoList[title];
                if(info.thumbnail){ //有缩略图
                    elem.addClass('card-media');
                    elem.find('.card-img').attr('src', info.thumbnail.source).show();
                }

                console.log(info);
                elem.find('.card-content').text(info.extract);
            } else {
                elem.find('.card-content').text('页面不存在');
            }
        });
    }

    api.get({
        action: 'query',
        prop: ['info', 'extracts', 'pageimages', 'revisions', 'info'],
        formatversion: 2,
        redirects: true,
        exintro: true,
        exchars: 150,
        explaintext: true,
        piprop: 'thumbnail',
        pithumbsize: 640,
        pilicense: 'any',
        rvprop: 'timestamp',
        inprop: 'url',
        titles: titleList,
        smaxage: 300,
        maxage: 300,
        uselang: 'content',
    }).done((data) => {
        if(data.query && data.query.pages && data.query.pages.length > 0){
            let pages = data.query.pages;
            pages.forEach((page) => {
                if(!page.missing){
                    pageInfoList[page.title] = page;
                }
            });
            setPreviews(pageInfoList);
        }
    });
});