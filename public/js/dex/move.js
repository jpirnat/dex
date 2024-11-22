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
			move: {},
			types: [],
			damageDealt: {},
			flags: [],
			methods: [],
			learnsetVgs: [],
			showAbilities: true,
			stats: [],

			hoverDamageDealt: null,

			hasMultipleGens: false,
			showOtherGens: false,
		};
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
			if (this.showOtherGens) {
				return this.learnsetVgs;
			}

			return this.learnsetVgs.filter(vg => vg.generationId === this.versionGroup.generationId);
		},
		visibleMethods() {
			if (this.showOtherGens) {
				return this.methods;
			}

			return this.methods.filter(m => {
				return this.visibleVersionGroups.some(vg => m.pokemons.some(p => vg.identifier in p.vgData));
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
				this.learnsetVgs = data.learnsetVgs;
				this.showAbilities = data.showAbilities;
				this.stats = data.stats;

				document.title = data.title;

				const showOtherGens = window.localStorage.getItem('dexMoveShowOtherGens') ?? 'false';
				this.showOtherGens = JSON.parse(showOtherGens);

				this.hasMultipleGens = false;
				let gens = {};
				this.learnsetVgs.forEach(vg => {
					gens[vg.generationId] = 1;
				});
				this.hasMultipleGens = Object.keys(gens).length > 1;
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
		toggleOtherGens() {
			this.showOtherGens = !this.showOtherGens;
			window.localStorage.setItem('dexMoveShowOtherGens', this.showOtherGens);
		},
	},
});
