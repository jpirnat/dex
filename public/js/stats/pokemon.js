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
		prevMonth: {},
		thisMonth: {},
		nextMonth: {},
		ratings: [],

		generation: {},
		stats: [],
		rawCount: null,
		averageWeight: null,
		viabilityCeiling: null,

		showAbilities: true,
		showItems: true,
		abilities: [],
		items: [],
		spreads: [],
		moves: [],
		teammates: [],
		counters: [],
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
				this.prevMonth = data.prevMonth;
				this.thisMonth = data.thisMonth;
				this.nextMonth = data.nextMonth;
				this.ratings = data.ratings;

				this.generation = data.generation;
				this.stats = data.stats;
				this.rawCount = data.rawCount;
				this.averageWeight = data.averageWeight;
				this.viabilityCeiling = data.viabilityCeiling;

				this.showAbilities = data.showAbilities;
				this.showItems = data.showItems;
				this.abilities = data.abilities;
				this.items = data.items;
				this.spreads = data.spreads;
				this.moves = data.moves;
				this.teammates = data.teammates;
				this.counters = data.counters;

				document.title = data.title;
			}
		});
	},
	methods: {
		addChartLine(line) {
			this.$refs.chart.addLine(line);
		},
	},
});
