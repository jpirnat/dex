'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		versionGroup: {},
		breadcrumbs: [],
		versionGroups: [],
		pokemon: {},
		abilities: [],
		types: [],
		abilitiesDamageTaken: {},
		damageTakenAbilities: [],
		methods: [],
		learnsetVgs: [],
		showMoveDescriptionsOption: true,

		hoverDamageTaken: null,
		damageTakenAbility: 'none',
		showOtherGens: false,
		showMoveDescriptions: true,
	},
	computed: {
		damageTaken() {
			return this.abilitiesDamageTaken[this.damageTakenAbility];
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
				return this.visibleVersionGroups.some(vg => m.moves.some(mo => mo.vgData[vg.identifier]));
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
				this.pokemon = data.pokemon;
				this.abilities = data.abilities;
				this.types = data.types;
				this.abilitiesDamageTaken = data.damageTaken;
				this.damageTakenAbilities = data.damageTakenAbilities;
				this.methods = data.methods;
				this.learnsetVgs = data.learnsetVgs;
				this.showMoveDescriptionsOption = data.showMoveDescriptions;

				document.title = data.title;

				// If the Pokémon's only ability gives it unique type matchups,
				// default to that ability in the matchups shown.
				if (this.damageTakenAbilities.length === 2 && this.abilities.length === 1) {
					this.damageTakenAbility = this.damageTakenAbilities[0].identifier;
				}

				const showOtherGens = window.localStorage.getItem('dexMoveShowOtherGens') ?? 'false';
				this.showOtherGens = JSON.parse(showOtherGens);

				const showMoveDescriptions = window.localStorage.getItem('dexPokemonShowMoveDescriptions') ?? 'true';
				this.showMoveDescriptions = JSON.parse(showMoveDescriptions);
			}
		});
	},
	methods: {
		onDamageTakenHover(multiplier) {
			this.hoverDamageTaken = multiplier;
		},
		onDamageTakenUnhover() {
			this.hoverDamageTaken = null;
		},
		toggleOtherGens() {
			this.showOtherGens = !this.showOtherGens;
			window.localStorage.setItem('dexMoveShowOtherGens', this.showOtherGens);
		},
		toggleMoveDescriptions() {
			this.showMoveDescriptions = !this.showMoveDescriptions;
			window.localStorage.setItem('dexPokemonShowMoveDescriptions', this.showMoveDescriptions);
		},
	},
});
