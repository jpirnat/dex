const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';
import DexMovesTable from '../dex-moves-table.js';

const app = createApp({
	components: {
		DexBreadcrumbs,
		DexMovesTable,
	},
	data() {
		return {
			loading: true,
			loaded: false,

			versionGroup: {},
			breadcrumbs: [],
			versionGroups: [],
			flag: {},
			moves: [],

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
				this.flag = data.flag;
				this.moves = data.moves;
			}

			const filterName = url.searchParams.get('name');
			const filterDescription = url.searchParams.get('description');
			if (filterName) {
				this.filterName = filterName;
			}
			if (filterDescription && this.versionGroup.hasMoveDescriptions) {
				this.filterDescription = filterDescription;
			}
		});
	},
	methods: {
		dexMoveFlagUrl(versionGroup) {
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

			return '/dex/' + versionGroup.identifier + '/move-flags/' + this.flag.identifier + queryParams;
		},
	},
});

app.mount('#app');
