(()=>{var e={153:e=>{e.exports=function(e,t){var a=e.split(".");"isekai"in window||(window.isekai={});for(var i=window.isekai,r=0;r<a.length-1;r++){var s=a[r];s in i||(i[s]={}),i=i[s]}i[a[r]]=t}}},t={};function a(i){var r=t[i];if(void 0!==r)return r.exports;var s=t[i]={exports:{}};return e[i](s,s.exports,a),s.exports}(()=>{function e(e,t){for(var a=0;a<t.length;a++){var i=t[a];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(e,i.key,i)}}a(153)("ui.CreatePageWidget",function(){function t(e){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t),this.baseDom=e,this.pageUrl=null,this.api=new mw.Api,this.hasError=!1,this.initDom()}var a,i;return a=t,(i=[{key:"initDom",value:function(){this.pageNameInput=new OO.ui.TextInputWidget({placeholder:mw.message("isekai-createpage-page-title").parse()}),this.pageNameInput.on("enter",this.createPage.bind(this)),this.pageNameInput.on("change",this.onPageNameChange.bind(this)),this.createButton=new OO.ui.ButtonWidget({label:mw.message("isekai-createpage-create-page-button").parse(),flags:["primary","progressive"]}),this.createButton.on("click",this.createPage.bind(this)),this.formGroup=new OO.ui.ActionFieldLayout(this.pageNameInput,this.createButton,{align:"top"}),this.baseDom.find(".card-body .card-content").append(this.formGroup.$element)}},{key:"createPage",value:function(){var e=this,t=this.pageNameInput.getValue();this.hasError&&this.clearError(),t.trim().length>0?(this.createButton.setDisabled(!0),this.pageExists(t).then((function(a){if(a)e.createButton.setDisabled(!1),e.setError(mw.message("isekai-createpage-page-exists").parse());else{var i=mw.util.getUrl(t,{veaction:"edit"});e.formGroup.setSuccess([mw.message("isekai-createpage-redirecting").parse()]),location.href=i}}))):this.setError(mw.message("isekai-createpage-title-empty").parse())}},{key:"onPageNameChange",value:function(){this.hasError&&this.clearError();var e=this.pageNameInput.getValue();if(-1!==e.indexOf("：")||-1!==e.indexOf("`")){var t=this.pageNameInput.getRange();e=e.replace(/：/g,":").replace(/`/g,"·"),this.pageNameInput.setValue(e),this.pageNameInput.selectRange(t.from,t.to)}}},{key:"setError",value:function(e){this.formGroup.setErrors([e]),this.hasError=!0}},{key:"clearError",value:function(){this.formGroup.setErrors([]),this.hasError=!1}},{key:"pageExists",value:function(e){var t=this;return new Promise((function(a,i){t.api.get({action:"query",titles:e}).done((function(e){e.query&&e.query.pages?e.query.pages[-1]?a(!1):a(!0):a(!1)})).fail(i)}))}},{key:"setTitle",value:function(e){this.title.text(e)}}])&&e(a.prototype,i),t}())})()})();