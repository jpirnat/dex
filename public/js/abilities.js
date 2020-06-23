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
	},
	computed: {
		filteredAbilities() {
			if (!this.filterName && !this.filterDescription) {
				return this.abilities;
			}

			return this.abilities.filter(a => 
				a.name.toLowerCase().includes(this.filterName.toLowerCase())
				&& a.description.toLowerCase().includes(this.filterDescription.toLowerCase())
			);
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
