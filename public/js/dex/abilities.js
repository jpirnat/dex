const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';
import DexAbilitiesTable from '../dex-abilities-table.js';

const app = createApp({
	components: {
		DexBreadcrumbs,
		DexAbilitiesTable,
	},
	data() {
		return {
			loading: true,
			loaded: false,

			versionGroup: {},
			breadcrumbs: [],
			versionGroups: [],
			abilities: [],
			flags: [],

			filterName: '',
			filterDescription: '',
		};
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
				this.versionGroup = data.versionGroup;
				this.breadcrumbs = data.breadcrumbs;
				this.versionGroups = data.versionGroups;
				this.abilities = data.abilities;
				this.flags = data.flags;
			}

			const filterName = url.searchParams.get('name');
			const filterDescription = url.searchParams.get('description');
			if (filterName) {
				this.filterName = filterName;
			}
			if (filterDescription) {
				this.filterDescription = filterDescription;
			}
		});
	},
	methods: {
		dexAbilitiesUrl(versionGroup) {
			let queryParams = [];
			if (this.filterName) {
				queryParams.push(`name=${encodeURIComponent(this.filterName)}`);
			}
			if (this.filterDescription) {
				queryParams.push(`description=${encodeURIComponent(this.filterDescription)}`);
			}
			queryParams = queryParams.length > 0
				? '?' + queryParams.join('&')
				: '';

			return '/dex/' + versionGroup.identifier + '/abilities' + queryParams;
		},
	},
});

app.mount('#app');
