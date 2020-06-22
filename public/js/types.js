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

		toggleAttackingIds: [],
		toggleDefendingIds: [],
		hoverAttackingId: null,
		hoverDefendingId: null
	},
	computed: {
		attackingTypes() {
			if (this.toggleAttackingIds.length === 0) {
				return this.types;
			}
			return this.types.filter(t => this.toggleAttackingIds.includes(t.id));
		},
		defendingTypes() {
			if (this.toggleDefendingIds.length === 0) {
				return this.types;
			}
			return this.types.filter(t => this.toggleDefendingIds.includes(t.id));
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
				this.types = data.types;
				this.multipliers = data.multipliers;

				this.types.forEach(t => {
					this.toggleAttackingIds.push(t.id);
					this.toggleDefendingIds.push(t.id);
				});
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
