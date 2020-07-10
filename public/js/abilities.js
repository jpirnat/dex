'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		breadcrumbs: [],
		generation: {},
		generations: [],
		abilities: [],

		filterName: '',
		filterDescription: '',

		currentPage: 1,
		itemsPerPage: 10,
	},
	computed: {
		filteredAbilities() {
			// 1) Filter. 2) Sort. 3) Paginate.
			if (!this.filterName && !this.filterDescription) {
				return this.abilities;
			}

			return this.abilities.filter(a => 
				a.name.toLowerCase().includes(this.filterName.toLowerCase())
				&& a.description.toLowerCase().includes(this.filterDescription.toLowerCase())
			);
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
				this.breadcrumbs = data.breadcrumbs;
				this.generation = data.generation;
				this.generations = data.generations;
				this.abilities = data.abilities;
			}
		});
	},
});
