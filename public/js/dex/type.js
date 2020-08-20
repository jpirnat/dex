'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		generation: {},
		breadcrumbs: [],
		generations: [],
		type: {},
		matchups: [],
		pokemons: [],
		showAbilities: true,
		stats: [],
		moves: [],
		showMoveDescriptions: true,
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
				this.generation = data.generation;
				this.breadcrumbs = data.breadcrumbs;
				this.generations = data.generations;
				this.type = data.type;
				this.matchups = data.matchups;
				this.pokemons = data.pokemons;
				this.showAbilities = data.showAbilities;
				this.stats = data.stats;
				this.moves = data.moves;
				this.showMoveDescriptions = this.showMoveDescriptions;

				document.title = data.title;
			}
		});
	},
});
