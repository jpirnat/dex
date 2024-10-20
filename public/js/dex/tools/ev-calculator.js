'use strict';

const app = new Vue({
	el: '#app',
	data: {
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

				this.addLevel();

				this.stats.forEach(s => {
					this.$set(this.ivs, s.identifier, 31);
					this.$set(this.evs, s.identifier, '???');
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
		evCalculatorUrl(versionGroup) {
			const queryParams = this.selectedPokemon !== null
				? `?pokemon=${this.selectedPokemon.identifier}`
				: '';
			return '/dex/' + versionGroup.identifier + '/tools/ev-calculator' + queryParams;
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
