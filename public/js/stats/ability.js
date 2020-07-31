'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		format: {},
		rating: 0,

		breadcrumbs: [],
		prevMonth: {},
		thisMonth: {},
		nextMonth: {},
		ratings: [],

		ability: {},
		pokemons: [],

		filterName: '',

		currentPage: 1,
		itemsPerPage: 20,

		sortColumn: '',
		sortDirection: '',
	},
	computed: {
		filteredPokemons() {
			if (!this.filterName) {
				return this.pokemons;
			}

			return this.pokemons.filter(
				p => p.name.toLowerCase().includes(this.filterName.toLowerCase())
			);
		},
		paginatedPokemons() {
			const start = (this.currentPage - 1) * this.itemsPerPage;
			const end = start + this.itemsPerPage;
			return this.filteredPokemons.slice(start, end);
		},
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
				this.breadcrumbs = data.breadcrumbs;
				this.prevMonth = data.prevMonth;
				this.thisMonth = data.thisMonth;
				this.nextMonth = data.nextMonth;
				this.ratings = data.ratings;
				this.ability = data.ability;
				this.pokemons = data.pokemons;

				document.title = data.title;
			}
		});
	},
	methods: {
		sortBy(column, defaultDirection) {
			if (this.sortColumn !== column) {
				// If we're not already sorted by this column, sort in its default direction.
				this.sortColumn = column;
				this.sortDirection = defaultDirection;
			} else {
				// If we're already sorted by this column, reverse the direction.
				this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
			}

			const modifier = this.sortDirection === 'asc' ? 1 : -1;

			// Do the sort.
			this.pokemons.sort((a, b) => {
				if (a[column] < b[column]) { return -1 * modifier; }
				if (a[column] > b[column]) { return +1 * modifier; }
				return 0;
			});
		},
		addChartLine(pokemon) {
			this.$refs.chart.addLine({
				type: 'usage-ability',
				format: this.format.identifier,
				rating: this.rating,
				pokemon: pokemon.identifier,
				ability: this.ability.identifier,
			});
		},
	},
	watch: {
		filterName() {
			this.currentPage = 1;
		},
	},
});
