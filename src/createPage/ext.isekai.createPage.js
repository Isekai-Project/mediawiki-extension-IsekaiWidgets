import { registerModule } from '../moduleRegister';

class CreatePageWidget {
    constructor(dom) {
        this.baseDom = dom;
        this.pageUrl = null;
        this.api = new mw.Api();

        this.hasError = false;

        this.initDom();
    }

    initDom() {
        this.pageNameInput = new OO.ui.TextInputWidget({
            placeholder: mw.message('isekai-createpage-page-title').parse(),
        });
        this.pageNameInput.on('enter', this.createPage.bind(this));
        this.pageNameInput.on('change', this.onPageNameChange.bind(this));

        this.createButton = new OO.ui.ButtonWidget({
            label: mw.message('isekai-createpage-create-page-button').parse(),
            flags: [
                'primary',
                'progressive'
            ]
        });
        this.createButton.on('click', this.createPage.bind(this));

        this.formGroup = new OO.ui.ActionFieldLayout(this.pageNameInput, this.createButton, {
            align: 'top'
        });
        this.baseDom.find('.card-body .card-content').append(this.formGroup.$element);
    }

    createPage() {
        let title = this.pageNameInput.getValue();
        if (this.hasError) {
            this.clearError(); //清除errors
        }
        if (title.trim().length > 0) {
            this.createButton.setDisabled(true);
            this.pageExists(title).then((exists) => {
                if (exists) {
                    this.createButton.setDisabled(false);
                    this.setError(mw.message('isekai-createpage-page-exists').parse()); //提示页面已经存在
                } else {
                    let targetUrl = mw.util.getUrl(title, { veaction: 'edit' });
                    this.formGroup.setSuccess([
                        mw.message('isekai-createpage-redirecting').parse()
                    ]); //提示正在跳转
                    location.href = targetUrl;
                }
            });
        } else {
            this.setError(mw.message('isekai-createpage-title-empty').parse());
        }
    }

    onPageNameChange() {
        if (this.hasError) {
            this.clearError();
        }

        let value = this.pageNameInput.getValue();
        if (value.indexOf('：') !== -1 || value.indexOf('`') !== -1) {
            let range = this.pageNameInput.getRange();
            value = value.replace(/：/g, ':').replace(/`/g, '·');
            this.pageNameInput.setValue(value);
            this.pageNameInput.selectRange(range.from, range.to);
        }
    }

    setError(msg) {
        this.formGroup.setErrors([msg]); //提示页面已经存在
        this.hasError = true;
    }

    clearError() {
        this.formGroup.setErrors([]);
        this.hasError = false;
    }

    pageExists(title) {
        return new Promise((resolve, reject) => {
            this.api.get({
                action: 'query',
                titles: title,
            }).done((data) => {
                if (data.query && data.query.pages) {
                    if (data.query.pages["-1"]) {
                        resolve(false);
                    } else {
                        resolve(true);
                    }
                } else {
                    resolve(false);
                }
            }).fail(reject);
        });
    }

    setTitle(title) {
        this.title.text(title);
    }
}

registerModule('ui.CreatePageWidget', CreatePageWidget);