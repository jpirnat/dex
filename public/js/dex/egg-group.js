const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';
import DexPokemonsTable from '../dex-pokemons-table.js';

const app = createApp({
    components: {
        DexBreadcrumbs,
        DexPokemonsTable,
    },
    data() {
        return {
            loading: true,
            loaded: false,

            versionGroup: {},
            breadcrumbs: [],
            versionGroups: [],
            eggGroup: {},
            pokemons: [],
            stats: [],

            filterName: '',
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
        this.eggGroup = data.eggGroup;
        this.pokemons = data.pokemons;
        this.stats = data.stats;

        document.title = data.title;
    },
});

app.mount('#app');
