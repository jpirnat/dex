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
			pokemons: [],
			showAbilities: true,
			stats: [],

			filterName: '',
		};
	},
	created() {
		const url = new URL(window.location);

		fetch('/data' + url.pathname, {
			credentials: 'same-origin'
		})
		.then(response => response.json())
		.then(response => {
			this.loading = false;
			this.loaded = true;

			if (response.data) {
				const data = response.data;
				this.versionGroup = data.versionGroup;
				this.breadcrumbs = data.breadcrumbs;
				this.versionGroups = data.versionGroups;
				this.pokemons = data.pokemons;
				this.showAbilities = data.showAbilities;
				this.stats = data.stats;
			}

			const filterName = url.searchParams.get('name');
			if (filterName) {
				this.filterName = filterName;
			}
		});
	},
	methods: {
		dexPokemonsUrl(versionGroup) {
			const queryParams = this.filterName !== ''
				? `?name=${encodeURIComponent(this.filterName)}`
				: '';
			return '/dex/' + versionGroup.identifier + '/pokemon' + queryParams;
		},
	},
});

app.mount('#app');
