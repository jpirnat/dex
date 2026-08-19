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

            breadcrumbs: [],

            years: [],
        };
    },
    async created() {
        const response = await fetch('/data/battle-data', {
            credentials: 'same-origin',
        })
        .then(response => response.json());

        this.loading = false;
        this.loaded = true;

        if (!response.data) {
            return;
        }

        const data = response.data;

        this.breadcrumbs = data.breadcrumbs;
        this.years = data.years;

        document.title = data.title;
    },
});

app.mount('#app');
