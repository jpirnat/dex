'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		generation: {},
		breadcrumbs: [],
		generations: [],
		abilities: [],

		filterName: '',
		filterDescription: '',

		currentPage: 1,
		itemsPerPage: 10,
	},
	computed: {
		filteredAbilities() {
			let filteredAbilities = this.abilities;

			if (this.filterName) {
				filteredAbilities = filteredAbilities.filter(a => a.name.toLowerCase().includes(
					this.filterName.toLowerCase()
				));
			}

			if (this.filterDescription) {
				filteredAbilities = filteredAbilities.filter(a => a.description.toLowerCase().includes(
					this.filterDescription.toLowerCase()
				));
			};

			return filteredAbilities;
		},
		paginatedAbilities() {
			const start = (this.currentPage - 1) * this.itemsPerPage;
			const end = start + this.itemsPerPage;
			return this.filteredAbilities.slice(start, end);
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
				this.generation = data.generation;
				this.breadcrumbs = data.breadcrumbs;
				this.generations = data.generations;
				this.abilities = data.abilities;
			}
		});
	},
	watch: {
		filterName() {
			this.currentPage = 1;
		},
		filterDescription() {
			this.currentPage = 1;
		},
	},
});
