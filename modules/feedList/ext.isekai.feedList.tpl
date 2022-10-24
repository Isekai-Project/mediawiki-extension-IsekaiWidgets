<div class="isekai-card isekai-feed-list-card">
    <div class="card-header">
        <span class="card-header-text"><?=wfMessage('isekai-feed-list-title')->parse()?></span>
    </div>
    <div class="card-body-fluid">
        <div id="isekai-feed-list" :class="{ mounted: 'mounted' }">
            <div v-if="loading" class="loading">
                <div class="spinner">
                    <div class="oo-ui-widget oo-ui-widget-enabled oo-ui-progressBarWidget-indeterminate oo-ui-progressBarWidget" aria-disabled="false" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                        <div class="oo-ui-progressBarWidget-bar"></div>
                    </div>
                </div>
            </div>
            <ul v-else class="isekai-list">
                <a class="isekai-list-item" v-for="(feedItem, index) in feedList" :key="index" :href="feedItem.url">
                    <div class="isekai-list-item-content">
                        <div class="isekai-list-item-title">{{ feedItem.title }}</div>
                        <div class="isekai-list-item-text">{{ feedItem.description }}</div>
                    </div>
                </a>
            </ul>
        </div>
    </div>
</div>