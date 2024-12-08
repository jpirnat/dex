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
			abilities: [],
			moves: [],
			stats: [],

			abilityName: '',
			selectedAbility: null,
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
		queryParams() {
			const queryParams = [];

			if (this.selectedAbility !== null) {
				const ability = this.selectedAbility.identifier;
				queryParams.push(`ability=${encodeURIComponent(ability)}`);
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
				this.abilities = data.abilities;
				this.moves = data.moves;
				this.stats = data.stats;
			}

			for (let i = 0; i < this.maxMovesetLength; i++) {
				this.moveNames[i] = '';
				this.selectedMoves[i] = null;
			}

			const includeTransferMoves = window.localStorage.getItem('dexPokemonShowTransferMoves') ?? 'false';
			this.includeTransferMoves = JSON.parse(includeTransferMoves);

			if (url.searchParams.size) {
				this.readUrlAndSearch();
			}
		});
	},
	methods: {
		readUrlAndSearch() {
			const url = new URL(window.location);
			const searchParams = url.searchParams;

			const ability = searchParams.get('ability');
			if (ability) {
				const exactAbility = this.abilities.find(a => a.identifier === ability);
				if (exactAbility) {
					this.selectedAbility = exactAbility;
					this.abilityName = exactAbility.name;
				}
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

			// TODO: Maybe turn this into a computed function?
			const moveIdentifiers = [];
			for (let i = 0; i < this.maxMovesetLength; i++) {
				if (this.selectedMoves[i] !== null) {
					moveIdentifiers.push(this.selectedMoves[i].identifier);
				}
			}

			this.loading = true;
			const response = await fetch(url.pathname, {
				method: 'POST',
				credentials: 'same-origin',
				headers: new Headers({
					'Content-Type': 'application/json',
				}),
				body: JSON.stringify({
					abilityIdentifier: this.selectedAbility !== null
						? this.selectedAbility.identifier
						: '',
					moveIdentifiers: moveIdentifiers,
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
