<script>
module.exports = {
    compatConfig: {
        MODE: 3
    },
    compilerOptions: {
        whitespace: 'condense'
    },
    setup() {
        const mounted = Vue.ref(false);
        const loading = Vue.ref(true);
        const feedList = Vue.ref([]);
        const api = new mw.Api();

        let recentData = {
            recentNew: null,
            recentEdit: null,
        };

        const onLoaded = () => {
            if (Array.isArray(recentData.recentNew) && Array.isArray(recentData.recentEdit)) {
                // 混合两个列表
                let recentList = [
                    ...recentData.recentNew,
                    ...recentData.recentEdit
                ];
                recentList.sort((a, b) => b.orderWeight - a.orderWeight);
                // 去除重复，获取pageid列表
                let pageIdList = [];
                recentList = recentList.filter((item) => {
                    if (pageIdList.includes(item.pageid)) {
                        return false;
                    } else {
                        pageIdList.push(item.pageid);
                        return true;
                    }
                });

                // 获取页面详细信息
                api.get({
                    "action": "query",
                    "prop": "extracts|info",
                    "pageids": pageIdList.join('|'),
                    "redirects": 1,
                    "converttitles": 1,
                    "exchars": 100,
                    "exintro": 1,
                    "explaintext": 1,
                    "inprop": "url"
                }).done((data) => {
                    if (data.query && data.query.pages) {
                        const pageInfoList = data.query.pages;
                        recentList = recentList.map((info) => {
                            if (info.pageid in pageInfoList) {
                                const pageInfo = pageInfoList[info.pageid];
                                return {
                                    pageid: info.pageid,
                                    title: pageInfo.title,
                                    description: pageInfo.extract,
                                    url: pageInfo.fullurl
                                }
                            } else {
                                return {
                                    pageid: info.pageid,
                                    title: info.title,
                                    description: '',
                                    url: mw.util.getUrl(info.title)
                                }
                            }
                        });
                        // 设置data
                        feedList.value = recentList;
                        loading.value = false;
                        console.log('feedList', feedList);
                    }
                });
            }
        };

        Vue.onMounted(() => {
            mounted.value = true;
            api.get({
                action: 'query',
                list: 'recentchanges',
                rctype: 'edit',
                rcnamespace: 0,
                rclimit: 20,
            }).done((data) => {
                recentData.recentEdit = [];
                if (data.query && Array.isArray(data.query.recentchanges)) { //有成功取到数据
                    data.query.recentchanges.forEach((one) => {
                        if (one.timestamp) {
                            one.timestamp = new Date(one.timestamp).getTime();
                            one.orderWeight = one.timestamp;
                        } else {
                            one.orderWeight = 0;
                        }
                        recentData.recentEdit.push(one);
                    });
                    onLoaded();
                }
            });

            api.get({
                action: 'query',
                list: 'recentchanges',
                rctype: 'new',
                rcnamespace: 0,
                rclimit: 20,
            }).done((data) => {
                recentData.recentNew = [];
                if (data.query && Array.isArray(data.query.recentchanges)) { // 成功取到数据
                    data.query.recentchanges.forEach((one) => {
                        if (one.timestamp) {
                            one.timestamp = new Date(one.timestamp).getTime();
                            one.orderWeight = one.timestamp + (86400 * 1000); // 新页面保护，权重比页面更新高7天
                        } else {
                            one.orderWeight = 0;
                        }
                        recentData.recentNew.push(one);
                    });
                    onLoaded();
                }
            });
        });

        return {
            mounted,
            loading,
            feedList
        }
    }
}
</script>

<template>
    <div class="isekai-feed-list" :class="{ mounted: mounted }">
        <div v-if="loading" class="loading">
            <div class="spinner">
                <div class="oo-ui-widget oo-ui-widget-enabled oo-ui-progressBarWidget-indeterminate oo-ui-progressBarWidget"
                    aria-disabled="false" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="oo-ui-progressBarWidget-bar"></div>
                </div>
            </div>
        </div>
        <ul v-else class="isekai-list">
            <a class="isekai-list-item" v-for="(feedItem, index) in feedList" :key="index" :href="feedItem.url"
                target="_blank">
                <div class="isekai-list-item-content">
                    <div class="isekai-list-item-title">{{ feedItem.title }}</div>
                    <div class="isekai-list-item-text">{{ feedItem.description }}</div>
                </div>
                <div class="isekai-list-item-icon">
                    <span
                        class="oo-ui-widget oo-ui-widget-enabled oo-ui-iconElement oo-ui-iconElement-icon oo-ui-image-progressive oo-ui-icon-next oo-ui-labelElement-invisible oo-ui-iconWidget"
                        aria-disabled="false"></span>
                </div>
            </a>
        </ul>
    </div>
</template>
