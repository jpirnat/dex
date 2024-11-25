const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';
import DexTypeLink from '../dex-type-link.js';
import DexPokemonsTable from '../dex-pokemons-table.js';
import DexMovesTable from '../dex-moves-table.js';

const app = createApp({
	components: {
		DexBreadcrumbs,
		DexTypeLink,
		DexPokemonsTable,
		DexMovesTable,
	},
	data() {
		return {
			loading: true,
			loaded: false,

			versionGroup: {},
			breadcrumbs: [],
			versionGroups: [],
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
				this.type = data.type;
				this.types = data.types;
				this.damageDealt = data.damageDealt;
				this.damageTaken = data.damageTaken;
				this.pokemons = data.pokemons;
				this.showAbilities = data.showAbilities;
				this.stats = data.stats;
				this.moves = data.moves;
				this.showMoveDescriptions = data.showMoveDescriptions;

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

app.mount('#app');
