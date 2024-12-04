const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';
import DexMovesTable from '../dex-moves-table.js';

const { vTooltip } = FloatingVue;
FloatingVue.options.themes.tooltip.delay.show = 0;

const app = createApp({
	components: {
		DexBreadcrumbs,
		DexMovesTable,
	},
	directives: {
		tooltip: vTooltip,
	},
	data() {
		return {
			loading: true,
			loaded: false,

			versionGroup: {},
			breadcrumbs: [],
			versionGroups: [],
			pokemons: [],
			types: [],
			categories: [],
			flags: [],

			showTypeFilters: false,
			filterTypes: [],
			showCategoryFilters: false,
			filterCategories: [],
			showFlagFilters: false,
			filterFlags: {},
			pokemonName: '',
			selectedPokemon: null,
			includeTransferMoves: false,
			searchHasBeenDone: false,

			moves: [],
			filterName: '',
			filterDescription: '',
		};
	},
	computed: {
		filteredPokemons() {
			if (!this.pokemonName) {
				return this.pokemons;
			}

			return this.pokemons.filter(p => p.name.toLowerCase().includes(this.pokemonName.toLowerCase()));
		},
		yesFlagIdentifiers() {
			return this.flags.filter(f => this.filterFlags[f.identifier] === 'yes').map(f => f.identifier);
		},
		noFlagIdentifiers() {
			return this.flags.filter(f => this.filterFlags[f.identifier] === 'no').map(f => f.identifier);
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
				this.types = data.types;
				this.categories = data.categories;
				this.flags = data.flags;
			}

			this.flags.forEach(f => this.filterFlags[f.identifier] = null);

			const showTypeFilters = window.localStorage.getItem('moveSearchShowTypeFilters') ?? 'false';
			this.showTypeFilters = JSON.parse(showTypeFilters);

			const showCategoryFilters = window.localStorage.getItem('moveSearchShowCategoryFilters') ?? 'false';
			this.showCategoryFilters = JSON.parse(showCategoryFilters);

			const showFlagFilters = window.localStorage.getItem('moveSearchShowFlagFilters') ?? 'false';
			this.showFlagFilters = JSON.parse(showFlagFilters);

			const includeTransferMoves = window.localStorage.getItem('dexPokemonShowTransferMoves') ?? 'false';
			this.includeTransferMoves = JSON.parse(includeTransferMoves);
		});
	},
	methods: {
		toggleTypeFilters() {
			this.showTypeFilters = !this.showTypeFilters;
			window.localStorage.setItem('moveSearchShowTypeFilters', this.showTypeFilters);
		},
		selectAllTypes() {
			this.filterTypes = this.types.map(t => t.identifier);
		},
		unselectAllTypes() {
			this.filterTypes = [];
		},

		toggleCategoryFilters() {
			this.showCategoryFilters = !this.showCategoryFilters;
			window.localStorage.setItem('moveSearchShowCategoryFilters', this.showCategoryFilters);
		},
		selectAllCategories() {
			this.filterCategories = this.categories.map(c => c.identifier);
		},
		unselectAllCategories() {
			this.filterCategories = [];
		},

		toggleFlagFilters() {
			this.showFlagFilters = !this.showFlagFilters;
			window.localStorage.setItem('moveSearchShowFlagFilters', this.showFlagFilters);
		},
		resetAllFlags() {
			this.flags.forEach(f => this.filterFlags[f.identifier] = null);
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
		clearPokemonName() {
			this.pokemonName = '';
			this.onChangePokemonName();
		},
		onChangeIncludeTransferMoves() {
			window.localStorage.setItem('dexPokemonShowTransferMoves', this.includeTransferMoves);
		},

		async search() {
			const url = new URL(window.location);

			this.loading = true;
			const response = await fetch(url.pathname, {
				method: 'POST',
				credentials: 'same-origin',
				headers: new Headers({
					'Content-Type': 'application/json',
				}),
				body: JSON.stringify({
					typeIdentifiers: this.filterTypes,
					categoryIdentifiers: this.filterCategories,
					yesFlagIdentifiers: this.yesFlagIdentifiers,
					noFlagsIdentifiers: this.noFlagIdentifiers,
					pokemonIdentifier: this.selectedPokemon !== null
						? this.selectedPokemon.identifier
						: '',
					includeTransferMoves: this.versionGroup.hasTransferMoves && this.includeTransferMoves,
				}),
			})
			.then(response => response.json())
			this.loading = false;
			this.searchHasBeenDone = true;
			this.filterName = '';
			this.filterDescription = '';

			if (response.data) {
				const data = response.data;

				this.moves = data.moves;
			}
		},
	},
});

app.mount('#app');
