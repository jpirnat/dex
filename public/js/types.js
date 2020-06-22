'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		breadcrumbs: [],
		generation: {},
		generations: [],
		types: [],
		multipliers: [],

		hoverAttackingId: null,
		hoverDefendingId: null
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
				this.types = data.types;
				this.multipliers = data.multipliers;
			}
		});
	},
	methods: {
		onMatchupHover(attackingType, defendingType) {
			this.hoverAttackingId = attackingType.id;
			this.hoverDefendingId = defendingType.id;
		},
		onMatchupUnhover() {
			this.hoverAttackingId = null;
			this.hoverDefendingId = null;
		},
	},
});
