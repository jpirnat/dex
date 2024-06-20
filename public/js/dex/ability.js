'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		versionGroup: {},
		breadcrumbs: [],
		versionGroups: [],
		ability: {},
		pokemons: [],
		showAbilities: true,
		stats: [],
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
				this.ability = data.ability;
				this.pokemons = data.pokemons;
				this.showAbilities = data.showAbilities;
				this.stats = data.stats;

				document.title = data.title;
			}
		});
	},
});
