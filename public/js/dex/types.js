'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		versionGroup: {},
		breadcrumbs: [],
		versionGroups: [],
		types: [],
		multipliers: [],

		toggleAttackingTypes: [],
		toggleDefendingTypes: [],
		hoverAttackingType: null,
		hoverDefendingType: null,
	},
	computed: {
		attackingTypes() {
			if (this.toggleAttackingTypes.length === 0) {
				return this.types;
			}
			return this.types.filter(t => this.toggleAttackingTypes.includes(t.identifier));
		},
		defendingTypes() {
			if (this.toggleDefendingTypes.length === 0) {
				return this.types;
			}
			return this.types.filter(t => this.toggleDefendingTypes.includes(t.identifier));
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
				this.types = data.types;
				this.multipliers = data.multipliers;

				this.types.forEach(t => {
					this.toggleAttackingTypes.push(t.identifier);
					this.toggleDefendingTypes.push(t.identifier);
				});
			}
		});
	},
	methods: {
		onMatchupHover(attackingType, defendingType) {
			this.hoverAttackingType = attackingType.identifier;
			this.hoverDefendingType = defendingType.identifier;
		},
		onMatchupUnhover() {
			this.hoverAttackingType = null;
			this.hoverDefendingType = null;
		},
	},
});
