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

		pokemons: [],

		filterName: '',

		currentPage: 1,
		itemsPerPage: 20,
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
				this.pokemons = data.pokemons;

				document.title = data.title;
			}
		});
	},
	watch: {
		filterName() {
			this.currentPage = 1;
		},
	},
});
