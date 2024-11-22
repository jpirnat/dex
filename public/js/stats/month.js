'use strict';

const app = new Vue({
	el: '#app',
	data() {
		return {
			loading: true,
			loaded: false,

			breadcrumbs: [],
			prevMonth: {},
			thisMonth: {},
			nextMonth: {},

			generations: [],
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
				this.prevMonth = data.prevMonth;
				this.thisMonth = data.thisMonth;
				this.nextMonth = data.nextMonth;
				this.generations = data.generations;

				document.title = data.title;
			}
		});
	},
});
