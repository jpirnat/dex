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
		showLeadsLink: false,

		pokemons: [],

		myFormat: '',
		myRating: 0,

		filterName: '',

		currentPage: 1,
		itemsPerPage: 20,
	},
	computed: {
		showSaveAsDefaultFormat() {
			return this.format.identifier !== this.myFormat
				|| this.rating !== this.myRating;
		},
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
				this.showLeadsLink = data.showLeadsLink;
				this.pokemons = data.pokemons;
				this.myFormat = data.myFormat;
				this.myRating = parseInt(data.myRating);

				document.title = data.title;
			}
		});
	},
	methods: {
		showMovesetLink(pokemon) {
			return pokemon.usagePercent >= .01;
		},
		saveAsDefaultFormat() {
			const formatIdentifier = this.format.identifier;
			const formatName = this.format.name;
			const rating = this.rating;

			const date = new Date();
			date.setFullYear(date.getFullYear() + 5);
			const expires = date.toUTCString();

			document.cookie = `format=${formatIdentifier}; path=/; expires=${expires}`;
			document.cookie = `rating=${rating}; path=/; expires=${expires}`;

			Swal.fire({
				icon: 'success',
				html: `${formatName} [${rating}] has been saved as your default format. `
					+ '<a href="/stats/current" target="_blank">Current Stats</a> will '
					+ `now always lead to the latest data for ${formatName} [${rating}].`
			});

			this.myFormat = formatIdentifier;
			this.myRating = rating;
		},
	},
	watch: {
		filterName() {
			this.currentPage = 1;
		},
	},
});
