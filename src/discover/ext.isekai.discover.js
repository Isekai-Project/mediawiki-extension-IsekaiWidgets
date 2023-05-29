import { registerModule } from '../moduleRegister';

class DiscoverWidget {
    constructor(dom){
        this.baseDom = dom;
        this.pageUrl = null;
        this.api = new mw.Api();
        
        this.initDom();
        this.refreshPage();
    }

    initDom(){
        this.reloadButton = new OO.ui.ButtonWidget({
            icon: 'reload',
            label: mw.message('isekai-discover-change-btn').parse(),
        });
        this.reloadButton.on('click', this.refreshPage.bind(this));
        
        this.readMoreButton = new OO.ui.ButtonWidget({
            icon: 'ellipsis',
            label: mw.message('isekai-discover-readmore-btn').parse(),
            flags: [
                'primary',
                'progressive'
            ]
        });
        this.readMoreButton.on('click', this.showMore.bind(this));

        this.loadingBar = new OO.ui.ProgressBarWidget({
            progress: false,
        });
        this.baseDom.find('.card-body .loading .spinner').append(this.loadingBar.$element);

        this.buttonGroup = new OO.ui.ButtonGroupWidget({
            items: [this.reloadButton, this.readMoreButton]
        });
        this.baseDom.find('.card-header .card-header-buttons').append(this.buttonGroup.$element);
        this.loading = this.baseDom.find('.card-body .loading');
        this.title = this.baseDom.find('.card-body .card-title');
        this.contentContainer = this.baseDom.find('.card-body .card-content');
    }

    showMore(){
        if(this.pageUrl){ //页面存在就跳转
            window.open(this.pageUrl);
        }
    }

    refreshPage(){
        this.pageUrl = null;
        this.clearContent();
        this.showLoading();
        this.getRandomPage().then((title) => {
            this.loadPage(title);
        });
    }

    setTitle(title){
        this.title.text(title);
    }

    showLoading(){
        this.loading.show();
        this.contentContainer.hide();
    }

    hideLoading(){
        this.loading.hide();
        this.contentContainer.show();
    }

    clearContent(){
        this.contentContainer.children().remove();
    }

    setContent(dom){
        this.hideLoading();
        this.clearContent();
        this.contentContainer.append(dom);
    }

    showError(msg){
        let errorMsg = new OO.ui.MessageWidget( {
            type: 'error',
            label: msg,
        });

        this.setContent(errorMsg.$element);
    }

    getRandomPage(){
        return new Promise((resolve, reject) => {
            this.api.get({
                action: 'query',
                list: 'random',
                rnlimit: 1,
                rnnamespace: 0,
            }).done((data) => {
                if(data.query && data.query.random && data.query.random.length > 0){
                    let title = data.query.random[0].title;
                    this.setTitle(title);
                    resolve(title);
                } else if(data.error){
                    this.showError(data.error.info);
                } else {
                    this.showError(mw.message('isekai-discover-error-cannotload').parse());
                }
            });
        });
    }

    parseHTMLString(txt) {
        try {
            let parser = new DOMParser();
            let xmlDoc = parser.parseFromString(txt, "text/html");
            return xmlDoc;
        } catch(e) {
            console.error(e.message);
        }
        return null;
    }

    loadPage(title){
        let url = mw.util.getUrl(title);
        this.pageUrl = url;
        if(url.indexOf('?') >= 0){
            url += '&';
        } else {
            url += '?'
        }
        url += 'action=render';
        $.get(url, (str) => {
            let dom = $(this.parseHTMLString(str));
            let content = dom.find('.mw-parser-output');
            if(content.length > 0){
                //删除目录
                content.find('.toc').remove();
                this.setContent(content);
            }
        }, 'html');
    }
}

registerModule('ui.DiscoverWidget', DiscoverWidget);