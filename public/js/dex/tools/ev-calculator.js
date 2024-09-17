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
		level: 100,
		ivs: {},
		finalStats: {},
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
			if (this.selectedPokemon === null
				|| this.level === ''
				|| this.selectedNature === null
			) {
				return true;
			}
			
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

				this.stats.forEach(s => {
					this.$set(this.ivs, s.identifier, 31);
					this.$set(this.finalStats, s.identifier, '');
					this.$set(this.evs, s.identifier, '???');
				});
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
				this.selectedPokemon = this.filteredPokemons[0];
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
					pokemonIdentifier: this.selectedPokemon.identifier,
					level: this.level,
					natureIdentifier: this.selectedNature.identifier,
					ivs: this.ivs,
					finalStats: this.finalStats,
					
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
