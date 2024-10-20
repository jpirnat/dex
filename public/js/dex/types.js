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

		joinCharacter: '-',
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

				const attackingJoined = url.searchParams.get('attacking');
				if (attackingJoined) {
					attackingJoined.split(this.joinCharacter).forEach(typeIdentifier => {
						this.toggleAttackingTypes.push(typeIdentifier);
					});
				} else {
					this.types.forEach(t => {
						this.toggleAttackingTypes.push(t.identifier);
					});
				}

				const defendingJoined = url.searchParams.get('defending');
				if (defendingJoined) {
					defendingJoined.split(this.joinCharacter).forEach(typeIdentifier => {
						this.toggleDefendingTypes.push(typeIdentifier);
					});
				} else {
					this.types.forEach(t => {
						this.toggleDefendingTypes.push(t.identifier);
					});
				}
			}
		});
	},
	methods: {
		updateUrl() {
			const url = new URL(window.location);
			url.searchParams.delete('attacking');
			url.searchParams.delete('defending');

			if (this.attackingTypes.length < this.types.length) {
				const attackingJoined = this.attackingTypes.map(t => t.identifier).join(this.joinCharacter);
				url.searchParams.set('attacking', attackingJoined);
			}
			if (this.defendingTypes.length < this.types.length) {
				const defendingJoined = this.defendingTypes.map(t => t.identifier).join(this.joinCharacter);
				url.searchParams.set('defending', defendingJoined);
			}

			history.replaceState({}, document.title, url.toString());
		},

		onMatchupHover(attackingType, defendingType) {
			this.hoverAttackingType = attackingType.identifier;
			this.hoverDefendingType = defendingType.identifier;
		},
		onMatchupUnhover() {
			this.hoverAttackingType = null;
			this.hoverDefendingType = null;
		},
	},
	watch: {
		attackingTypes() {
			this.updateUrl();
		},
		defendingTypes() {
			this.updateUrl();
		},
	},
});
