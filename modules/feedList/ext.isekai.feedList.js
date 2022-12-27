const Vue = require("vue");

if (document.querySelector('#isekai-feed-list')) {
    const App = require("./FeedList.vue");
    Vue.createMwApp(App).mount("#isekai-feed-list");
}