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
		stats: [],
		types: [],
		abilitiesDamageTaken: {},
		damageTakenAbilities: [],
		evolutionTableRows: [],
		methods: [],
		learnsetVgs: [],

		hoverDamageTaken: null,
		damageTakenAbility: 'none',
	},
	computed: {
		damageTaken() {
			return this.abilitiesDamageTaken[this.damageTakenAbility];
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
				this.pokemon = data.pokemon;
				this.stats = data.stats;
				this.types = data.types;
				this.abilitiesDamageTaken = data.damageTaken;
				this.damageTakenAbilities = data.damageTakenAbilities;
				this.evolutionTableRows = data.evolutionTableRows;
				this.methods = data.methods;
				this.learnsetVgs = data.learnsetVgs;

				document.title = data.title;

				// If the Pokémon's only ability gives it unique type matchups,
				// default to that ability in the matchups shown.
				if (this.damageTakenAbilities.length === 2 && this.pokemon.abilities.length === 1) {
					this.damageTakenAbility = this.damageTakenAbilities[0].identifier;
				}
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
	},
});
