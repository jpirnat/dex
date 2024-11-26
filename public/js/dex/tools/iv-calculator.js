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
			characteristics: [],
			types: [],
			stats: [],

			pokemonName: '',
			natureName: '',
			characteristicName: '',
			hpTypeName: '',
			selectedPokemon: null,
			selectedNature: null,
			selectedCharacteristic: null,
			selectedHpType: null,
			atLevel: [],
			ivs: {},
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
		filteredCharacteristics() {
			if (!this.characteristicName) {
				return this.characteristics;
			}

			return this.characteristics.filter(c => c.name.toLowerCase().includes(this.characteristicName.toLowerCase()));
		},
		filteredHpTypes() {
			if (!this.hpTypeName) {
				return this.types;
			}

			return this.types.filter(t => t.name.toLowerCase().includes(this.hpTypeName.toLowerCase()));
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
				this.characteristics = data.characteristics;
				this.types = data.types;
				this.stats = data.stats;
			}

			this.addLevel();

			this.stats.forEach(s => {
				this.ivs[s.identifier] = '???';
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
		onChangeCharacteristicName() {
			if (this.characteristicName === '') {
				this.selectedCharacteristic = null;
				return;
			}

			if (this.filteredCharacteristics.length === 1) {
				this.selectedCharacteristic = this.filteredCharacteristics[0];
				return;
			}
		},
		onChangeHpTypeName() {
			if (this.hpTypeName === '') {
				this.selectedHpType = null;
				return;
			}

			if (this.filteredHpTypes.length === 1) {
				this.selectedHpType = this.filteredHpTypes[0];
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
		clearCharacteristicName() {
			this.characteristicName = '';
			this.onChangeCharacteristicName();
		},
		clearHpTypeName() {
			this.hpTypeName = '';
			this.onChangeHpTypeName();
		},

		addLevel() {
			let previousLevel = 0;
			let previousFinal = {};
			let previousEvs = {};
			this.stats.forEach(s => {
				previousEvs[s.identifier] = 0;
			});

			if (this.atLevel.length > 0) {
				previousLevel = this.atLevel[this.atLevel.length - 1].level;
				previousFinal = this.atLevel[this.atLevel.length - 1].finalStats;
				previousEvs = this.atLevel[this.atLevel.length - 1].evs;
			}

			const finalStats = {};
			const evs = {};
			this.stats.forEach(s => {
				finalStats[s.identifier] = previousFinal[s.identifier];
			});
			this.stats.forEach(s => {
				evs[s.identifier] = previousEvs[s.identifier];
			});

			this.atLevel.push({
				level: Math.min(previousLevel + 1, 100),
				finalStats: finalStats,
				evs: evs,
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
					characteristicIdentifier: this.selectedCharacteristic !== null
						? this.selectedCharacteristic.identifier
						: '',
					hpTypeIdentifier: this.selectedHpType !== null
						? this.selectedHpType.identifier
						: '',
					atLevel: this.atLevel,
				}),
			})
			.then(response => response.json())

			if (response.data) {
				const data = response.data;

				this.ivs = data.ivs;
			}
		},
	},
});

app.mount('#app');
