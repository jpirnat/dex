const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';
import DexPagination from '../dex-pagination.js';
import DexChartDrawer from '../dex-chart-drawer.js';

const app = createApp({
	components: {
		DexBreadcrumbs,
		DexPagination,
		DexChartDrawer,
	},
	data() {
		return {
			loading: true,
			loaded: false,

			format: {},
			rating: 0,

			breadcrumbs: [],
			prevMonth: {},
			thisMonth: {},
			nextMonth: {},
			ratings: [],
			speedName: '',
			pokemons: [],
			months: [],

			filterName: '',

			currentPage: 1,
			itemsPerPage: 20,

			sortColumn: '',
			sortDirection: '',

			start: '',
			end: '',
		};
	},
	computed: {
		queryParams() {
			if (this.filterName) {
				return `?name=${encodeURIComponent(this.filterName)}`;
			}

			return '';
		},
		filteredPokemons() {
			let filteredPokemons = this.pokemons;

			if (this.filterName) {
				filteredPokemons = filteredPokemons.filter(p => p.name.toLowerCase().includes(
					this.filterName.toLowerCase()
				));
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
				this.format = data.format;
				this.rating = data.rating;
				this.breadcrumbs = data.breadcrumbs;
				this.prevMonth = data.prevMonth;
				this.thisMonth = data.thisMonth;
				this.nextMonth = data.nextMonth;
				this.ratings = data.ratings;
				this.speedName = data.speedName;
				this.pokemons = data.pokemons;
				this.months = data.months;

				this.start = data.thisMonth.value;
				this.end = data.thisMonth.value;

				document.title = data.title;
			}

			const filterName = url.searchParams.get('name');
			if (filterName) {
				this.filterName = filterName;
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
				type: 'lead-usage',
				format: this.format.identifier,
				rating: this.rating,
				pokemon: pokemon.identifier,
			});
		},
		goToAveraged() {
			window.location.assign(`/stats/${this.start}-to-${this.end}/${this.format.identifier}/${this.rating}/leads${this.queryParams}`);
		},
	},
	watch: {
		filterName() {
			this.currentPage = 1;
		},
	},
});

app.mount('#app');
