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
			characteristicName: '',
			hpTypeName: '',
			selectedPokemon: null,
			selectedNature: null,
			level: 100,
			friendship: 255,
			ivs: {},
			evs: {},
			avs: {},
			effortLevels: {},
			finalStats: {},
			cp: 0,
		};
	},
	computed: {
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
		const queryPokemonIdentifier = url.searchParams.get('pokemon');

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

				this.stats.forEach(s => {
					this.ivs[s.identifier] = this.versionGroup.maxIv;
					this.evs[s.identifier] = 0;
					this.avs[s.identifier] = 0;
					this.effortLevels[s.identifier] = 0;
					this.finalStats[s.identifier] = '???';
				});

				if (queryPokemonIdentifier) {
					const exactPokemon = this.pokemons.find(p => p.identifier === queryPokemonIdentifier);
					if (exactPokemon) {
						this.selectedPokemon = exactPokemon;
					}
				}
			}
		});
	},
	methods: {
		statCalculatorUrl(versionGroup) {
			const queryParams = this.selectedPokemon !== null
				? `?pokemon=${this.selectedPokemon.identifier}`
				: '';
			return '/dex/' + versionGroup.identifier + '/tools/stat-calculator' + queryParams;
		},

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
					level: this.level,
					friendship: this.friendship,
					ivs: this.ivs,
					evs: this.evs,
					avs: this.avs,
					effortLevels: this.effortLevels,
				}),
			})
			.then(response => response.json())

			if (response.data) {
				const data = response.data;

				this.finalStats = data.finalStats;
				this.cp = data.cp;
			}
		},
	},
});

app.mount('#app');
