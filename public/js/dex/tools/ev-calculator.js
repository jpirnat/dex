const { createApp } = Vue;

import DexBreadcrumbs from '../../dex-breadcrumbs.js';
import DexTypeLink from '../../dex-type-link.js';

const app = createApp({
	components: {
		DexBreadcrumbs,
		DexTypeLink,
	},
	data() {
		return {
			loading: true,
			loaded: false,

			versionGroup: {},
			breadcrumbs: [],
			versionGroups: [],
			pokemons: [],
			natures: [],
			stats: [],

			pokemonName: '',
			natureName: '',
			selectedPokemon: null,
			selectedNature: null,
			atLevel: [],
			ivs: {},
			evs: {},
		};
	},
	computed: {
		queryParams() {
			const queryParams = [];

			if (this.selectedPokemon !== null) {
				queryParams.push(`pokemon=${this.selectedPokemon.identifier}`);
			}

			return queryParams.length > 0
				? '?' + queryParams.join('&')
				: '';
		},
		filteredPokemons() {
			if (!this.pokemonName) {
				return this.pokemons;
			}

			return this.pokemons.filter(p => p.name.toLowerCase().includes(this.pokemonName.toLowerCase()));
		},
		filteredNatures() {
			if (!this.natureName) {
				return this.natures;
			}

			return this.natures.filter(n => n.expandedName.toLowerCase().includes(this.natureName.toLowerCase()));
		},
		disableCalculate() {
			if (this.selectedPokemon === null) {
				return true;
			}

			return false;
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
				this.pokemons = data.pokemons;
				this.natures = data.natures;
				this.stats = data.stats;
			}

			this.addLevel();

			this.stats.forEach(s => {
				this.ivs[s.identifier] = 31;
				this.evs[s.identifier] = '???';
			});

			const queryPokemonIdentifier = url.searchParams.get('pokemon');
			if (queryPokemonIdentifier) {
				const exactPokemon = this.pokemons.find(p => p.identifier === queryPokemonIdentifier);
				if (exactPokemon) {
					this.selectedPokemon = exactPokemon;
					this.pokemonName = exactPokemon.name;
				}
			}
		});
	},
	methods: {
		onChangePokemonName() {
			if (this.pokemonName === '') {
				this.selectedPokemon = null;
				return;
			}

			const exactPokemon = this.filteredPokemons.find(p => p.name.toLowerCase() === this.pokemonName.toLowerCase());
			if (exactPokemon) {
				this.selectedPokemon = exactPokemon;
				return;
			}

			if (this.filteredPokemons.length === 1) {
				this.selectedPokemon = this.filteredPokemons[0];
				return;
			}
		},
		onChangeNatureName() {
			if (this.natureName === '') {
				this.selectedNature = null;
				return;
			}

			if (this.filteredNatures.length === 1) {
				this.selectedNature = this.filteredNatures[0];
				return;
			}
		},


		clearPokemonName() {
			this.pokemonName = '';
			this.onChangePokemonName();
		},
		clearNatureName() {
			this.natureName = '';
			this.onChangeNatureName();
		},

		addLevel() {
			let previousLevel = 0;
			let previousFinal = {};

			if (this.atLevel.length > 0) {
				previousLevel = this.atLevel[this.atLevel.length - 1].level;
				previousFinal = this.atLevel[this.atLevel.length - 1].finalStats;
			}

			const finalStats = {};
			this.stats.forEach(s => {
				finalStats[s.identifier] = previousFinal[s.identifier];
			});

			this.atLevel.push({
				level: Math.min(previousLevel + 1, 100),
				finalStats: finalStats,
			});
		},
		removeLevel(atLevelIndex) {
			this.atLevel.splice(atLevelIndex, 1);
		},

		async calculate() {
			if (this.disableCalculate) {
				return;
			}

			const url = new URL(window.location);

			const response = await fetch(url.pathname, {
				method: 'POST',
				credentials: 'same-origin',
				headers: new Headers({
					'Content-Type': 'application/json',
				}),
				body: JSON.stringify({
					pokemonIdentifier: this.selectedPokemon !== null
						? this.selectedPokemon.identifier
						: '',
					natureIdentifier: this.selectedNature !== null
						? this.selectedNature.identifier
						: '',
					ivs: this.ivs,
					atLevel: this.atLevel,
				}),
			})
			.then(response => response.json())

			if (response.data) {
				const data = response.data;

				this.evs = data.evs;
			}
		},
	},
});

app.mount('#app');
