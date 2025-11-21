const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';

const { vTooltip } = FloatingVue;
FloatingVue.options.themes.tooltip.delay.show = 0;

const app = createApp({
    components: {
        DexBreadcrumbs,
    },
    directives: {
        tooltip: vTooltip,
    },
    data() {
        return {
            loading: true,
            loaded: false,

            versionGroup: {},
            breadcrumbs: [],
            versionGroups: [],
            item: {},
            evolutions: [],
        };
    },
    async created() {
        const url = new URL(window.location);

        const response = await fetch('/data' + url.pathname, {
            credentials: 'same-origin',
        })
        .then(response => response.json());

        this.loading = false;
        this.loaded = true;

        if (!response.data) {
            return;
        }

        const data = response.data;
        this.versionGroup = data.versionGroup;
        this.breadcrumbs = data.breadcrumbs;
        this.versionGroups = data.versionGroups;
        this.item = data.item;
        this.evolutions = data.evolutions;
    },
});

app.mount('#app');
