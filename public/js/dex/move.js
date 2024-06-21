'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		versionGroup: {},
		breadcrumbs: [],
		versionGroups: [],
		move: {},
		types: [],
		damageDealt: {},
		flags: [],
		methods: [],
		dexVersionGroups: [],
		showAbilities: true,
		stats: [],

		hoverDamageDealt: null,

		showOlderGames: false,
	},
	computed: {
		showOtherDetails() {
			return this.move.minHits > 0
				|| this.move.infliction
				|| this.move.minTurns > 0
				|| this.move.critRate > 0
				|| this.move.flinchPercent > 0
				|| this.move.recoilPercent !== 0
				|| this.move.healPercent !== 0
				|| this.statChanges.length > 0
			;
		},
		visibleVersionGroups() {
			if (this.showOlderGames) {
				return this.dexVersionGroups;
			}

			return this.dexVersionGroups.filter(vg => vg.generationId === this.versionGroup.generationId);
		},
		visibleMethods() {
			if (this.showOlderGames) {
				return this.methods;
			}

			return this.methods.filter(m => {
				return this.visibleVersionGroups.some(vg => m.pokemons.some(p => p.vgData[vg.identifier]));
			});
		}
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
				this.move = data.move;
				this.types = data.types;
				this.damageDealt = data.damageDealt;
				this.statChanges = data.statChanges;
				this.flags = data.flags;
				this.methods = data.methods;
				this.dexVersionGroups = data.dexVersionGroups;
				this.showAbilities = data.showAbilities;
				this.stats = data.stats;

				document.title = data.title;

				const showOlderGames = window.localStorage.getItem('dexMoveShowOlderGames') ?? 'false';
				this.showOlderGames = JSON.parse(showOlderGames);
			}
		});
	},
	methods: {
		powerText(move) {
			if (move.power === 0) {
				return '—'; // em dash
			}
			if (move.power === 1) {
				return '*';
			}
			return move.power;
		},
		accuracyText(move) {
			if (move.accuracy === 101) {
				return '—'; // em dash
			}
			return move.accuracy + '%';
		},
		onDamageDealtHover(multiplier) {
			this.hoverDamageDealt = multiplier;
		},
		onDamageDealtUnhover() {
			this.hoverDamageDealt = null;
		},
		toggleOlderGames() {
			this.showOlderGames = !this.showOlderGames;
			window.localStorage.setItem('dexMoveShowOlderGames', this.showOlderGames);
		},
	},
});
