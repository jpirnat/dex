const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';

const app = createApp({
	components: {
		DexBreadcrumbs,
	},
	data() {
		return {
			loading: true,
			loaded: false,

			breadcrumbs: [],

			years: [],
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

				this.breadcrumbs = data.breadcrumbs;
				this.years = data.years;

				document.title = data.title;
			}
		});
	},
});

app.mount('#app');
