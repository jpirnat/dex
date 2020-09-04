'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		generation: {},
		breadcrumbs: [],
		generations: [],
		type: {},
		types: [],
		damageDealt: {},
		damageTaken: {},
		pokemons: [],
		showAbilities: true,
		stats: [],
		moves: [],
		showMoveDescriptions: true,

		hoverDamageDealt: null,
		hoverDamageTaken: null,
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
				this.generation = data.generation;
				this.breadcrumbs = data.breadcrumbs;
				this.generations = data.generations;
				this.type = data.type;
				this.types = data.types;
				this.damageDealt = data.damageDealt;
				this.damageTaken = data.damageTaken;
				this.pokemons = data.pokemons;
				this.showAbilities = data.showAbilities;
				this.stats = data.stats;
				this.moves = data.moves;
				this.showMoveDescriptions = this.showMoveDescriptions;

				document.title = data.title;
			}
		});
	},
	methods: {
		onDamageDealtHover(multiplier) {
			this.hoverDamageDealt = multiplier;
		},
		onDamageTakenHover(multiplier) {
			this.hoverDamageTaken = multiplier;
		},
		onDamageDealtUnhover() {
			this.hoverDamageDealt = null;
		},
		onDamageTakenUnhover() {
			this.hoverDamageTaken = null;
		},
	},
});
