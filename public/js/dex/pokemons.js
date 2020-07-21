'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		breadcrumbs: [],
		generation: {},
		generations: [],
		showAbilities: true,
		stats: [],
		pokemons: [],

		filterName: '',

		currentPage: 1,
		itemsPerPage: 10,

		sortColumn: '',
		sortDirection: '',
	},
	computed: {
		filteredPokemons() {
			let filteredPokemons = this.pokemons;

			if (this.filterName) {
				filteredPokemons = filteredPokemons.filter(p => p.name.toLowerCase().includes(
					this.filterName.toLowerCase())
				);
			}

			return filteredPokemons;
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
				this.breadcrumbs = data.breadcrumbs;
				this.generation = data.generation;
				this.generations = data.generations;
				this.showAbilities = data.showAbilities;
				this.stats = data.stats;
				this.pokemons = data.pokemons;
			}
		});
	},
	methods: {
		sortBy(column, defaultDirection, sortValueCallback) {
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
				const aValue = sortValueCallback(a);
				const bValue = sortValueCallback(b);

				if (aValue < bValue) { return -1 * modifier; }
				if (aValue > bValue) { return +1 * modifier; }
				return 0;
			});
		},
	},
	watch: {
		filterName() {
			this.currentPage = 1;
		},
	},
});
