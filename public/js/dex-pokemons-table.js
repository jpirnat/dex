import DexPagination from './dex-pagination.js';
import DexTypeLink from './dex-type-link.js';

const { vTooltip } = FloatingVue;
FloatingVue.options.themes.tooltip.delay.show = 0;

export default {
	name: 'dex-pokemons-table',
	components: {
		DexPagination,
		DexTypeLink,
	},
	directives: {
		tooltip: vTooltip,
	},
	props: {
		pokemons: {
			type: Array,
			default: [],
		},
		versionGroup: {
			// Required fields: identifier, hasAbilities, hasBreeding, hasEvYields, hasEvBasedStats
			type: Object,
			default: {},
		},
		stats: {
			type: Array,
			default: []
		},
		filterName: {
			type: String,
			default: '',
		},
	},
	emits: ['update:filterName'],
	data() {
		return {
			currentPage: 1,
			itemsPerPage: 10,

			currentTab: 'baseStats',

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
				v-model:current-page="currentPage"
				:number-of-items="filteredPokemons.length"
				:items-per-page="itemsPerPage"
			></dex-pagination>

			<div class="dex-pokemons__control">
				<label class="dex-pokemons__filter">
					Filter by Pokémon name: <input type="search" :value="filterName" @input="$emit('update:filterName', $event.target.value)">
				</label>

				<div class="dex-pokemons__control-space"></div>

				<template v-if="versionGroup.hasBreeding || versionGroup.hasEvYields">
					<button @click="showTab('baseStats')"
						:class="{
							'button': true,
							'button--active': currentTab === 'baseStats'
						}"
					>
						 Base Stats
					</button>
					<button v-if="versionGroup.hasBreeding" @click="showTab('breeding')"
						:class="{
							'button': true,
							'button--active': currentTab === 'breeding'
						}"
					>
						Breeding
					</button>
					<button v-if="versionGroup.hasEvYields" @click="showTab('evYields')"
						:class="{
							'button': true,
							'button--active': currentTab === 'evYields'
						}"
					>
						EV Yields
					</button>
				</template>
			</div>

			<p v-if="currentTab === 'evYields' && versionGroup.hasEvYields && !versionGroup.hasEvBasedStats">
				Even though EVs don't contribute to a Pokémon's stats in this game, they can
				still be gained by defeating other Pokémon, and the EVs will take effect if the
				Pokémon is transferred to another game. Weird!
			</p>

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
						<th v-if="versionGroup.hasAbilities" scope="col">Abilities</th>
						<template v-if="currentTab === 'baseStats'">
							<th v-for="stat in stats" :key="stat.identifier" scope="col" class="dex-table--number dex-table__header--sortable"
								@click="sortBy('base-' + stat.identifier, 'desc', p => p.baseStats[stat.identifier])"
								:class="{
									['dex-table__stat--' + stat.identifier]: true,
									'dex-table__header--sorted-asc': sortColumn === 'base-' + stat.identifier && sortDirection === 'asc',
									'dex-table__header--sorted-desc': sortColumn === 'base-' + stat.identifier && sortDirection === 'desc',
								}"
								v-tooltip="stat.name"
							>
								<abbr class="dex--tooltip">{{ stat.abbreviation }}</abbr>
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
						</template>
						<template v-if="currentTab === 'breeding'">
							<th scope="col">Egg Groups</th>
							<th scope="col">Gender Ratio</th>
							<th scope="col" class="dex-table--number dex-table__header--sortable"
								@click="sortBy('eggCycles', 'asc', p => p.eggCycles)"
								:class="{
									'dex-table__header--sorted-asc': sortColumn === 'eggCycles' && sortDirection === 'asc',
									'dex-table__header--sorted-desc': sortColumn === 'eggCycles' && sortDirection === 'desc',
								}"
							>Egg Cycles</th>
							<th scope="col" class="dex-table--number dex-table__header--sortable"
								@click="sortBy('stepsToHatch', 'asc', p => p.stepsToHatch)"
								:class="{
									'dex-table__header--sorted-asc': sortColumn === 'stepsToHatch' && sortDirection === 'asc',
									'dex-table__header--sorted-desc': sortColumn === 'stepsToHatch' && sortDirection === 'desc',
								}"
							>Steps to Hatch</th>
						</template>
						<template v-if="currentTab === 'evYields'">
							<th v-for="stat in stats" :key="stat.identifier" scope="col" class="dex-table--number dex-table__header--sortable"
								@click="sortBy('ev-' + stat.identifier, 'desc', p => p.evYield[stat.identifier] ? p.evYield[stat.identifier] : 0)"
								:class="{
									['dex-table__stat--' + stat.identifier]: true,
									'dex-table__header--sorted-asc': sortColumn === 'ev-' + stat.identifier && sortDirection === 'asc',
									'dex-table__header--sorted-desc': sortColumn === 'ev-' + stat.identifier && sortDirection === 'desc',
								}"
								v-tooltip="stat.name"
							>
								<abbr class="dex--tooltip">{{ stat.abbreviation }}</abbr>
							</th>
						</template>
					</tr>
				</thead>
				<tbody>
					<tr v-for="pokemon in paginatedPokemons" :key="pokemon.identifier">
						<td class="dex-table__pokemon-icon">
							<img v-if="pokemon.icon" class="dex-pokemon-icon" :src="'/images/pokemon/icons/' + pokemon.icon" alt="">
						</td>
						<td class="dex-table__pokemon-name">
							<a :href="'/dex/' + versionGroup.identifier + '/pokemon/' + pokemon.identifier">
								{{ pokemon.name }}
							</a>
						</td>
						<td class="dex-table__pokemon-types">
							<div v-for="type in pokemon.types" :key="type.identifier">
								<dex-type-link
									:vg-identifier="versionGroup.identifier"
									:type="type"
								></dex-type-link>
							</div>
						</td>
						<td v-if="versionGroup.hasAbilities">
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
						<template v-if="currentTab === 'baseStats'">
							<td v-for="stat in stats" :key="stat.identifier" class="dex-table--number"
								:class="{
									['dex-table__stat--' + stat.identifier]: true,
								}"
							>
								{{ pokemon.baseStats[stat.identifier] }}
							</td>
							<td class="dex-table--number">{{ pokemon.bst }}</td>
						</template>
						<template v-if="currentTab === 'breeding'">
							<td>
								<div class="dex-table__pokemon-egg-groups">
									<a v-for="eggGroup in pokemon.eggGroups" :key="eggGroup.identifier"
										:href="'/dex/' + versionGroup.identifier + '/egg-groups/' + eggGroup.identifier"
									>
										{{ eggGroup.name }}
									</a>
								</div>
							</td>
							<td class="dex-table--icon" v-tooltip="pokemon.genderRatio.description">
								<img :src="'/images/gender-ratios/' + pokemon.genderRatio.icon" :alt="pokemon.genderRatio.description">
							</td>
							<td class="dex-table--number">{{ pokemon.eggCycles }}</td>
							<td class="dex-table--number">{{ pokemon.stepsToHatch }}</td>
						</template>
						<template v-if="currentTab === 'evYields'">
							<td v-for="stat in stats" :key="stat.identifier" class="dex-table--number"
								:class="{
									['dex-table__stat--' + stat.identifier]: pokemon.evYield[stat.identifier],
								}"
							>
								{{ pokemon.evYield[stat.identifier] ? pokemon.evYield[stat.identifier] : 0 }}
							</td>
						</template>
					</tr>
				</tbody>
			</table>

			<dex-pagination
				v-model:current-page="currentPage"
				:number-of-items="filteredPokemons.length"
				:items-per-page="itemsPerPage"
			></dex-pagination>
		</div>
	`,
	methods: {
		showTab(tab) {
			this.currentTab = tab;
		},

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
		pokemons() {
			this.currentPage = 1;
		},
		filterName() {
			this.currentPage = 1;
		},
	},
};
