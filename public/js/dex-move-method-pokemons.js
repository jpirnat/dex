'use strict';

Vue.component('dex-move-method-pokemons', {
	props: {
		method: {
			type: Object,
			default: {},
		},
		generation: {
			type: Object,
			default: {},
		},
		move: {
			type: Object,
			default: {},
		},
		versionGroups: {
			type: Array,
			default: [],
		},
		stats: {
			type: Array,
			default: [],
		},
		showAbilities: {
			type: Boolean,
			default: true,
		},
	},
	data() {
		return {
			sortColumn: '',
			sortDirection: '',
		};
	},
	computed: {
		colspan() {
			return 4 + this.versionGroups.length + this.stats.length + (this.showAbilities ? 1 : 0);
		},
		visiblePokemons() {
			return this.method.pokemons.filter(p => this.versionGroups.some(vg => p.vgData[vg.identifier]));
		},
	},
	template: `
		<tbody :id="'via-' + method.identifier">
			<tr class="dex-table__sticky-header-1">
				<th :colspan="colspan">
					<template v-if="method.description">
						{{ method.name }} - {{ method.description }}
					</template>
					<template v-else>
						{{ method.name }}
					</template>
				</th>
			</tr>
			<tr class="dex-table__sticky-header-2">
				<th v-for="vg in versionGroups" :key="vg.identifier"
					scope="col" class="dex-table--icon dex-table__pokemon-move-data"
					v-tooltip="vg.name"
				>
					<img :src="'/images/versions/' + vg.icon" :alt="vg.name">
				</th>
				<th class="dex-table__header--sortable dex-move__pokemon-icon"
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
			<tr v-for="pokemon in visiblePokemons" :key="pokemon.identifier">
				<template v-for="vg in versionGroups" :key="vg.identifier">
					<template v-if="pokemon.vgData[vg.identifier] !== undefined">
						<template v-if="method.identifier === 'level-up'">
							<td class="dex-table__pokemon-move-data dex-table--number"
								v-tooltip="pokemonMoveTooltip(pokemon, move, vg, method)"
							>
								{{ pokemon.vgData[vg.identifier] }}
							</td>
						</template>

						<template v-else-if="method.identifier === 'machine'">
							<td class="dex-table__pokemon-move-data"
								v-tooltip="pokemonMoveTooltip(pokemon, move, vg, method)"
							>
								{{ pokemon.vgData[vg.identifier] }}
							</td>
						</template>

						<template v-else-if="method.identifier === 'egg'">
							<td class="dex-table__pokemon-move-data dex-table--icon"
								v-tooltip="pokemonMoveTooltip(pokemon, move, vg, method)"
							>
								<a :href="
										'/dex/' + generation.identifier + '/pokemon/' + pokemon.identifier
										+ '/breeding/' + move.identifier + '/' + vg.identifier
									"
									target="_blank"
								>
									<img src="/images/miscellaneous/egg.png" alt="Egg Move">
								</a>
							</td>
						</template>

						<template v-else-if="method.identifier === 'light-ball'">
							<td class="dex-table__pokemon-move-data dex-table--icon"
								v-tooltip="pokemonMoveTooltip(pokemon, move, vg, method)"
							>
								<img src="/images/miscellaneous/egg.png" alt="Egg Move">
							</td>
						</template>

						<template v-else>
							<td class="dex-table__pokemon-move-data dex-table--icon"
								v-tooltip="pokemonMoveTooltip(pokemon, move, vg, method)"
							>
								<img :src="'/images/versions/' + vg.icon" :alt="vg.name">
							</td>
						</template>
					</template>
					<template v-else>
						<td class="dex-table__pokemon-move-data"></td>
					</template>
				</template>
				<td class="dex-table__pokemon-icon dex-move__pokemon-icon">
					<img :src="'/images/pokemon/icons/' + pokemon.icon" alt="">
				</td>
				<td class="dex-table__pokemon-name">
					<a :href="'/dex/' + generation.identifier + '/pokemon/' + pokemon.identifier">
						{{ pokemon.name }}
					</a>
				</td>
				<td class="dex-table__pokemon-types">
					<a v-for="type in pokemon.types" :key="type.identifier"
						:href="'/dex/' + generation.identifier + '/types/' + type.identifier"
					>
						<img :src="'/images/types/' + type.icon" :alt="type.name">
					</a>
				</td>
				<td v-if="showAbilities">
					<div class="dex-table__pokemon-abilities">
						<a v-for="ability in pokemon.abilities" :key="ability.identifier"
							:href="'/dex/' + generation.identifier + '/abilities/' + ability.identifier"
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
			this.method.pokemons.sort((a, b) => {
				const aValue = sortValueCallback(a);
				const bValue = sortValueCallback(b);

				if (aValue < bValue) { return -1 * modifier; }
				if (aValue > bValue) { return +1 * modifier; }
				return 0;
			});
		},
		pokemonMoveTooltip(pokemon, move, vg, method) {
			if (method.identifier === 'level-up') {
				return `${pokemon.name} learns ${move.name} in ${vg.name} at Level ${pokemon.vgData[vg.identifier]}.`;
			}
			if (method.identifier === 'machine') {
				return `${pokemon.name} learns ${move.name} in ${vg.name} at via ${pokemon.vgData[vg.identifier]}.`;
			}
			if (method.identifier === 'egg') {
				return `${pokemon.name} learns ${move.name} in ${vg.name} as an Egg Move. Click for breeding chains.`;
			}
			if (method.identifier === 'sketch') {
				return `${pokemon.name} learns ${move.name} in ${vg.name} via ${method.name}.`;
			}
			if (method.identifier === 'tutor') {
				return `${pokemon.name} learns ${move.name} in ${vg.name} via ${method.name}.`;
			}
			if (method.identifier === 'light-ball') {
				return `${pokemon.name} learns ${move.name} in ${vg.name} as an Egg Move if a parent is holding a ${method.name}.`;
			}
			if (method.identifier === 'form-change') {
				return `${pokemon.name} learns ${move.name} in ${vg.name} when it changes to this form.`;
			}
			if (method.identifier === 'shadow') {
				return `${pokemon.name} learns ${move.name} in ${vg.name} as a ${method.name} move.`;
			}
			if (method.identifier === 'purification') {
				return `${pokemon.name} learns ${move.name} in ${vg.name} when it is purified.`;
			}
			return '';
		},
	},
});
