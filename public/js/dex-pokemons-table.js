'use strict';

Vue.component('dex-pokemons-table', {
	props: {
		pokemons: {
			type: Array,
			default: [],
		},
		versionGroup: {
			type: Object,
			default: {},
		},
		showAbilities: {
			type: Boolean,
			default: true,
		},
		stats: {
			type: Array,
			default: []
		},
	},
	data() {
		return {
			filterName: '',

			currentPage: 1,
			itemsPerPage: 10,

			sortColumn: '',
			sortDirection: '',
		};
	},
	computed: {
		filteredPokemons() {
			let filteredPokemons = this.pokemons;

			if (this.filterName) {
				filteredPokemons = filteredPokemons.filter(p => p.name.toLowerCase().includes(
					this.filterName.toLowerCase()
				));
			}

			return filteredPokemons;
		},
		paginatedPokemons() {
			const start = (this.currentPage - 1) * this.itemsPerPage;
			const end = start + this.itemsPerPage;
			return this.filteredPokemons.slice(start, end);
		},
	},
	template: `
		<div>
			<dex-pagination
				v-model="currentPage"
				:number-of-items="filteredPokemons.length"
				:items-per-page="itemsPerPage"
			></dex-pagination>

			<div class="dex-pokemons__filters">
				<label class="dex-pokemons__filter">
					Filter by Pokémon name: <input type="search" v-model="filterName">
				</label>
			</div>

			<table class="dex-table dex-table--full-width">
				<thead>
					<tr>
						<th class="dex-table__header--sortable"
							@click="sortBy('sort', 'asc', p => p.sort)"
							:class="{
								'dex-table__header--sorted-asc': sortColumn === 'sort' && sortDirection === 'asc',
								'dex-table__header--sorted-desc': sortColumn === 'sort' && sortDirection === 'desc',
							}"
						></th>
						<th scope="col" class="dex-table__pokemon-name dex-table__header--sortable"
							@click="sortBy('name', 'asc', p => p.name)"
							:class="{
								'dex-table__header--sorted-asc': sortColumn === 'name' && sortDirection === 'asc',
								'dex-table__header--sorted-desc': sortColumn === 'name' && sortDirection === 'desc',
							}"
						>Pokémon</th>
						<th scope="col">Types</th>
						<th v-if="showAbilities" scope="col">Abilities</th>
						<th v-for="stat in stats" :key="stat.key" scope="col" class="dex-table--number dex-table__header--sortable"
							@click="sortBy(stat.key, 'desc', p => p.baseStats[stat.key])"
							:class="{
								'dex-table__header--sorted-asc': sortColumn === stat.key && sortDirection === 'asc',
								'dex-table__header--sorted-desc': sortColumn === stat.key && sortDirection === 'desc',
							}"
							v-tooltip="stat.name"
						>
							<abbr class="dex--tooltip">{{ stat.abbr }}</abbr>
						</th>
						<th scope="col" class="dex-table--number dex-table__header--sortable"
							@click="sortBy('bst', 'desc', p => p.bst)"
							:class="{
								'dex-table__header--sorted-asc': sortColumn === 'bst' && sortDirection === 'asc',
								'dex-table__header--sorted-desc': sortColumn === 'bst' && sortDirection === 'desc',
							}"
							v-tooltip="'Base Stat Total'"
						>
							<abbr class="dex--tooltip">BST</abbr>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="pokemon in paginatedPokemons" :key="pokemon.identifier">
						<td class="dex-table__pokemon-icon">
							<img :src="'/images/pokemon/icons/' + pokemon.icon" alt="">
						</td>
						<td class="dex-table__pokemon-name">
							<a :href="'/dex/' + versionGroup.identifier + '/pokemon/' + pokemon.identifier">
								{{ pokemon.name }}
							</a>
						</td>
						<td class="dex-table__pokemon-types">
							<div v-for="type in pokemon.types" :key="type.identifier">
								<dex-type-link
									:vgIdentifier="versionGroup.identifier"
									:type="type"
								></dex-type-link>
							</div>
						</td>
						<td v-if="showAbilities">
							<div class="dex-table__pokemon-abilities">
								<a v-for="ability in pokemon.abilities" :key="ability.identifier"
									:href="'/dex/' + versionGroup.identifier + '/abilities/' + ability.identifier"
									class="dex-table__pokemon-ability"
									:class="{
										'dex-table__pokemon-ability--hidden': ability.isHiddenAbility,
									}"
								>
									{{ ability.name }}
								</a>
							</div>
						</td>
						<td v-for="stat in stats" :key="stat.key" class="dex-table--number">
							{{ pokemon.baseStats[stat.key] }}
						</td>
						<td class="dex-table--number">{{ pokemon.bst }}</td>
					</tr>
				</tbody>
			</table>

			<dex-pagination
				v-model="currentPage"
				:number-of-items="filteredPokemons.length"
				:items-per-page="itemsPerPage"
			></dex-pagination>
		</div>
	`,
	methods: {
		sortBy(column, defaultDirection, sortValueCallback) {
			if (this.sortColumn !== column) {
				// If we're not already sorted by this column, sort in its default direction.
				this.sortColumn = column;
				this.sortDirection = defaultDirection;
			} else {
				// If we're already sorted by this column, reverse the direction.
				this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
			}

			const modifier = this.sortDirection === 'asc' ? 1 : -1;

			// Do the sort.
			this.pokemons.sort((a, b) => {
				const aValue = sortValueCallback(a);
				const bValue = sortValueCallback(b);

				if (aValue < bValue) { return -1 * modifier; }
				if (aValue > bValue) { return +1 * modifier; }
				return 0;
			});
		},
	},
	watch: {
		filterName() {
			this.currentPage = 1;
		},
	},
});
