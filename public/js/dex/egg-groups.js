const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';

const app = createApp({
    components: {
        DexBreadcrumbs,
    },
    data() {
        return {
            loading: true,
            loaded: false,

            versionGroup: {},
            breadcrumbs: [],
            versionGroups: [],
            eggGroups: [],
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
        this.eggGroups = data.eggGroups;
    },
});

app.mount('#app');
