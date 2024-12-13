const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';
import DexPokemonsTable from '../dex-pokemons-table.js';

const app = createApp({
	components: {
		DexBreadcrumbs,
		DexPokemonsTable,
	},
	data() {
		return {
			loading: true,
			loaded: false,

			versionGroup: {},
			breadcrumbs: [],
			versionGroups: [],
			types: [],
			abilities: [],
			eggGroups: [],
			genderRatios: [],
			moves: [],
			stats: [],

			showTypeFilters: false,
			selectedTypes: [],
			typesOperator: 'both',

			showAbilityFilters: false,
			abilityName: '',
			selectedAbility: null,

			showBreedingFilters: false,
			selectedEggGroups: [],
			eggGroupsOperator: 'any',

			selectedGenderRatios: [],
			genderRatiosOperator: 'any',

			showMoveFilters: false,
			maxMovesetLength: 4,
			moveNames: [],
			selectedMoves: [],
			includeTransferMoves: false,

			searchHasBeenDone: false,
			joinCharacter: '.',

			pokemons: [],
			filterName: '',
		};
	},
	computed: {
		filteredAbilities() {
			if (!this.abilityName) {
				return this.abilities;
			}

			return this.abilities.filter(a => a.name.toLowerCase().includes(this.abilityName.toLowerCase()));
		},
		filteredMoves() {
			const filteredMoves = [];

			for (let i = 0; i < this.maxMovesetLength; i++) {
				if (this.moveNames[i]) {
					filteredMoves[i] = this.moves.filter(m => m.name.toLowerCase().includes(this.moveNames[i].toLowerCase()));
				} else {
					filteredMoves[i] = this.moves;
				}
			}

			return filteredMoves;
		},
		selectedMoveIdentifiers() {
			const moveIdentifiers = [];

			for (let i = 0; i < this.maxMovesetLength; i++) {
				if (this.selectedMoves[i] !== null) {
					moveIdentifiers.push(this.selectedMoves[i].identifier);
				}
			}

			return moveIdentifiers;
		},
		queryParams() {
			const queryParams = [];

			if (this.selectedTypes.length > 0) {
				const typesJoined = this.selectedTypes.join(this.joinCharacter);
				queryParams.push(`types=${encodeURIComponent(typesJoined)}`);
				if (this.selectedTypes.length > 1) {
					queryParams.push(`typesOperator=${encodeURIComponent(this.typesOperator)}`);
				}
			}

			if (this.selectedAbility !== null) {
				const ability = this.selectedAbility.identifier;
				queryParams.push(`ability=${encodeURIComponent(ability)}`);
			}

			if (this.selectedEggGroups.length > 0) {
				const eggGroupsJoined = this.selectedEggGroups.join(this.joinCharacter);
				queryParams.push(`eggGroups=${encodeURIComponent(eggGroupsJoined)}`);
				if (this.selectedEggGroups.length > 1) {
					queryParams.push(`eggGroupsOperator=${encodeURIComponent(this.eggGroupsOperator)}`);
				}
			}

			if (this.selectedGenderRatios.length > 0) {
				const genderRatiosJoined = this.selectedGenderRatios.join(this.joinCharacter);
				queryParams.push(`genderRatios=${encodeURIComponent(genderRatiosJoined)}`);
				queryParams.push(`genderRatiosOperator=${encodeURIComponent(this.genderRatiosOperator)}`);
			}

			const selectedMoves = this.selectedMoves.filter(m => m !== null);
			if (selectedMoves.length > 0) {
				const movesJoined = selectedMoves.map(m => m.identifier).join(this.joinCharacter);
				queryParams.push(`moves=${encodeURIComponent(movesJoined)}`);

				if (this.versionGroup.hasTransferMoves && this.includeTransferMoves) {
					queryParams.push(`includeTransferMoves=true`);
				}
			}

			return queryParams.length > 0
				? '?' + queryParams.join('&')
				: '';
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
				this.types = data.types;
				this.abilities = data.abilities;
				this.eggGroups = data.eggGroups;
				this.genderRatios = data.genderRatios;
				this.moves = data.moves;
				this.stats = data.stats;
			}

			for (let i = 0; i < this.maxMovesetLength; i++) {
				this.moveNames[i] = '';
				this.selectedMoves[i] = null;
			}

			const includeTransferMoves = window.localStorage.getItem('dexPokemonShowTransferMoves') ?? 'false';
			this.includeTransferMoves = JSON.parse(includeTransferMoves);

			const showTypeFilters = window.localStorage.getItem('pokemonSearchShowTypeFilters') ?? 'false';
			this.showTypeFilters = JSON.parse(showTypeFilters);

			const showAbilityFilters = window.localStorage.getItem('pokemonSearchShowAbilityFilters') ?? 'false';
			this.showAbilityFilters = JSON.parse(showAbilityFilters);

			const showBreedingFilters = window.localStorage.getItem('pokemonSearchShowBreedingFilters') ?? 'false';
			this.showBreedingFilters = JSON.parse(showBreedingFilters);

			const showMoveFilters = window.localStorage.getItem('pokemonSearchShowMoveFilters') ?? 'false';
			this.showMoveFilters = JSON.parse(showMoveFilters);

			if (url.searchParams.size) {
				this.readUrlAndSearch();
			}
		});
	},
	methods: {
		readUrlAndSearch() {
			const url = new URL(window.location);
			const searchParams = url.searchParams;

			const types = searchParams.get('types');
			if (types) {
				types.split(this.joinCharacter).forEach(t => {
					this.selectedTypes.push(t);
				});
				const typesOperator = searchParams.get('typesOperator');
				this.typesOperator = typesOperator ? typesOperator : 'any';
			}

			const ability = searchParams.get('ability');
			if (ability) {
				const exactAbility = this.abilities.find(a => a.identifier === ability);
				if (exactAbility) {
					this.selectedAbility = exactAbility;
					this.abilityName = exactAbility.name;
				}
			}

			const eggGroups = searchParams.get('eggGroups');
			if (eggGroups) {
				eggGroups.split(this.joinCharacter).forEach(e => {
					this.selectedEggGroups.push(e);
				});
				const eggGroupsOperator = searchParams.get('eggGroupsOperator');
				this.eggGroupsOperator = eggGroupsOperator ? eggGroupsOperator : 'any';
			}

			const genderRatios = searchParams.get('genderRatios');
			if (genderRatios) {
				genderRatios.split(this.joinCharacter).forEach(g => {
					this.selectedGenderRatios.push(g);
				});
				const genderRatiosOperator = searchParams.get('genderRatiosOperator');
				this.genderRatiosOperator = genderRatiosOperator ? genderRatiosOperator : 'any';
			}

			const moves = searchParams.get('moves');
			if (moves) {
				let i = 0;
				moves.split(this.joinCharacter).forEach(mIdentifier => {
					if (i >= this.maxMovesetLength) {
						return;
					}

					const exactMove = this.moves.find(m => m.identifier === mIdentifier);
					if (exactMove) {
						this.selectedMoves[i] = exactMove;
						this.moveNames[i] = exactMove.name;
						i++;
					}
				});
			}

			const includeTransferMoves = searchParams.get('includeTransferMoves');
			if (includeTransferMoves) {
				this.includeTransferMoves = true;
			}

			this.search();
		},

		toggleTypeFilters() {
			this.showTypeFilters = !this.showTypeFilters;
			window.localStorage.setItem('pokemonSearchShowTypeFilters', this.showTypeFilters);
		},
		onChangeSelectedTypes() {
			if (this.selectedTypes.length > 2 && this.typesOperator === 'both') {
				this.typesOperator = 'any';
			}
		},

		toggleAbilityFilters() {
			this.showAbilityFilters = !this.showAbilityFilters;
			window.localStorage.setItem('pokemonSearchShowAbilityFilters', this.showAbilityFilters);
		},
		onChangeAbilityName() {
			if (this.abilityName === '') {
				this.selectedAbility = null;
				return;
			}

			const exactAbility = this.filteredAbilities.find(a => a.name.toLowerCase() === this.abilityName.toLowerCase());
			if (exactAbility) {
				this.selectedAbility = exactAbility;
				return;
			}

			if (this.filteredAbilities.length === 1) {
				this.selectedAbility = this.filteredAbilities[0];
				return;
			}
		},
		clearAbilityName() {
			this.abilityName = '';
			this.onChangeAbilityName();
		},

		toggleBreedingFilters() {
			this.showBreedingFilters = !this.showBreedingFilters;
			window.localStorage.setItem('pokemonSearchShowBreedingFilters', this.showBreedingFilters);
		},
		onChangeSelectedEggGroups() {
			if (this.selectedEggGroups.length > 2 && this.eggGroupsOperator === 'both') {
				this.eggGroupsOperator = 'any';
			}
		},

		toggleMoveFilters() {
			this.showMoveFilters = !this.showMoveFilters;
			window.localStorage.setItem('pokemonSearchShowMoveFilters', this.showMoveFilters);
		},
		onChangeMoveName(i) {
			if (this.moveNames[i] === '') {
				this.selectedMoves[i] = null;
				return;
			}

			const exactMove = this.filteredMoves[i].find(m => m.name.toLowerCase() === this.moveNames[i].toLowerCase());
			if (exactMove) {
				this.selectedMoves[i] = exactMove;
				return;
			}

			if (this.filteredMoves[i].length === 1) {
				this.selectedMoves[i] = this.filteredMoves[i][0];
				return;
			}
		},
		clearMoveName(i) {
			this.moveNames[i] = '';
			this.onChangeMoveName(i);
		},
		onChangeIncludeTransferMoves() {
			window.localStorage.setItem('dexPokemonShowTransferMoves', this.includeTransferMoves);
		},

		updateUrlAndSearch() {
			this.updateUrl();
			this.search();
		},
		updateUrl() {
			const url = new URL(window.location);
			history.replaceState({}, document.title, url.pathname + this.queryParams);
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
					typeIdentifiers: this.selectedTypes,
					typesOperator: this.typesOperator,
					abilityIdentifier: this.selectedAbility !== null
						? this.selectedAbility.identifier
						: '',
					eggGroupIdentifiers: this.selectedEggGroups,
					eggGroupsOperator: this.eggGroupsOperator,
					genderRatios: this.selectedGenderRatios,
					genderRatiosOperator: this.genderRatiosOperator,
					moveIdentifiers: this.selectedMoveIdentifiers,
					includeTransferMoves: this.versionGroup.hasTransferMoves && this.includeTransferMoves,
				}),
			})
			.then(response => response.json())
			this.loading = false;
			this.searchHasBeenDone = true;
			this.filterName = '';

			if (response.data) {
				const data = response.data;

				this.pokemons = data.pokemons;
			}
		},
	},
});

app.mount('#app');
