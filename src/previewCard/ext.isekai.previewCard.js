import { registerModule } from '../moduleRegister';

class PreviewCardWidget {
    constructor(dom) {
        this.baseDom = dom;
        this.pageName = null;
        this.api = new mw.Api();

        this.loaded = false;

        this.initDom();
    }

    initDom() {
        this.pageName = this.baseDom.attr('data-title');
        
        this.title = this.baseDom.find('card-title');
    }

    load() {
        if(this.loaded) return;

        
    }
}

registerModule('ui.PreviewCardWidget', PreviewCardWidget);