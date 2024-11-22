'use strict';

const app = new Vue({
	el: '#app',
	data() {
		return {
			loading: true,
			loaded: false,

			versionGroup: {},
			breadcrumbs: [],
			versionGroups: [],
			moves: [],
			showMoveDescriptions: true,
			flags: [],
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
				this.moves = data.moves;
				this.showMoveDescriptions = data.showMoveDescriptions;
				this.flags = data.flags;
			}
		});
	},
});
