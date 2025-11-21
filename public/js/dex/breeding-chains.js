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

            breadcrumbs: [],
            pokemon: {},
            move: {},
            chains: [],
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
        this.breadcrumbs = data.breadcrumbs;
        this.pokemon = data.pokemon;
        this.move = data.move;
        this.chains = data.chains;

        document.title = data.title;
    },
    methods: {
        toggleChain(chain) {
            chain.show = !chain.show;
        },
    },
});

app.mount('#app');
