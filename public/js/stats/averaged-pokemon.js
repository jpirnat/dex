'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		format: {},
		rating: 0,
		pokemon: {},

		breadcrumbs: [],
		startMonth: {},
		endMonth: {},
		ratings: [],

		versionGroup: {},
		generation: {},
		stats: [],

		showAbilities: true,
		showItems: true,
		abilities: [],
		items: [],
		moves: [],
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

				this.format = data.format;
				this.rating = data.rating;
				this.pokemon = data.pokemon;

				this.breadcrumbs = data.breadcrumbs;
				this.startMonth = data.startMonth;
				this.endMonth = data.endMonth;
				this.ratings = data.ratings;

				this.versionGroup = data.versionGroup;
				this.generation = data.generation;
				this.stats = data.stats;

				this.showAbilities = data.showAbilities;
				this.showItems = data.showItems;
				this.abilities = data.abilities;
				this.items = data.items;
				this.moves = data.moves;

				document.title = data.title;
			}
		});
	},
});
