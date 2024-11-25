const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';
import DexPagination from '../dex-pagination.js';

const app = createApp({
	components: {
		DexBreadcrumbs,
		DexPagination,
	},
	data() {
		return {
			loading: true,
			loaded: false,

			versionGroup: {},
			breadcrumbs: [],
			versionGroups: [],
			items: [],
			showItemIcons: true,
			showItemDescriptions: true,

			filterName: '',
			filterDescription: '',

			currentPage: 1,
			itemsPerPage: 20,
		};
	},
	computed: {
		filteredItems() {
			let filteredItems = this.items;

			if (this.filterName) {
				filteredItems = filteredItems.filter(a => a.name.toLowerCase().includes(
					this.filterName.toLowerCase()
				));
			}

			if (this.filterDescription) {
				filteredItems = filteredItems.filter(a => a.description.toLowerCase().includes(
					this.filterDescription.toLowerCase()
				));
			}

			return filteredItems;
		},
		paginatedItems() {
			const start = (this.currentPage - 1) * this.itemsPerPage;
			const end = start + this.itemsPerPage;
			return this.filteredItems.slice(start, end);
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
				this.versionGroup = data.versionGroup;
				this.breadcrumbs = data.breadcrumbs;
				this.versionGroups = data.versionGroups;
				this.items = data.items;
				this.showItemIcons = data.showItemIcons;
				this.showItemDescriptions = data.showItemDescriptions;
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

app.mount('#app');
