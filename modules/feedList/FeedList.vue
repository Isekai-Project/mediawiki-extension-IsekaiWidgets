<script>
module.exports = {
    compatConfig: {
        MODE: 3
    },
    compilerOptions: {
        whitespace: 'condense'
    },
    setup() {
        const bbsUrl = 'https://bbs.isekai.cn';

        const mounted = Vue.ref(false);
        const loading = Vue.ref(true);
        const feedList = Vue.ref([]);
        const api = new mw.Api();

        let recentData = {
            recentNew: null,
            recentEdit: null,
            recentThread: null,
        };

        let requiredData = ['recentNew', 'recentEdit'];
        let externalData = [];

        const onLoaded = () => {
            if (!requiredData.every((key) => Array.isArray(recentData[key]))) {
                return false;
            }
            if (!externalData.every((key) => Array.isArray(recentData[key]))) {
                return false;
            }

            // 混合两个列表
            let recentList = [];
            requiredData.forEach((key) => {
                recentList.push(...recentData[key]);
            });
            externalData.forEach((key) => {
                recentList.push(...recentData[key]);
            });

            recentList.sort((a, b) => b.orderWeight - a.orderWeight);

            // 去除重复，获取pageid列表
            let pageIdList = [];
            recentList = recentList.filter((item) => {
                if (item.external) { // 不过滤外部页面
                    return true;
                }

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
                        if (info.external) {
                            return {
                                pageid: -1,
                                ...info,
                            };
                        } else if (info.pageid in pageInfoList) {
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
                }
            });
        };

        const loadData = () => {
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

            if (bbsUrl) {
                externalData.push('recentThread');
                let formatter = document.createElement('div');
                fetch('/api/bbs/recent').then((res) => {
                    if (res.ok) {
                        return res.json();
                    } else {
                        throw new Error('Cannot load bbs threads: HTTP ' + res.status + ' ' + res.statusText);
                    }
                }).then((data) => {
                    recentData.recentThread = [];
                    if (data && Array.isArray(data.topics)) {
                        data.topics.forEach((topicData) => {
                            let data = {
                                external: true,
                                siteName: '异世界红茶馆',
                                title: topicData.title,
                                orderWeight: new Date(topicData.timestamp).getTime(),
                                url: bbsUrl + '/topic/' + topicData.slug,
                            }
                            if (topicData.teaser) {
                                data.url += '/' + topicData.teaser.index.toString();
                                // 去除HTML标签
                                formatter.innerHTML = topicData.teaser.content
                                data.description = formatter.innerText;
                            }
                            recentData.recentThread.push(data);
                        });
                    }
                    onLoaded();
                }).catch(console.error);
            } else {
                recentData.recentThread = [];
            }
        }

        Vue.onMounted(() => {
            mounted.value = true;
            loadData();
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
    <div class="isekai-feed-list isekai-thin-scrollbar" :class="{ mounted: mounted }">
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
                    <div class="isekai-list-item-title">
                        <div>{{ feedItem.title }}</div>
                        <div v-if="feedItem.siteName" class="tag">{{ feedItem.siteName }}</div>
                    </div>
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
