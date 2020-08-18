'use strict';

Vue.component('dex-pokemon-method-moves', {
	props: {
		method: {
			type: Object,
			default: {},
		},
		generation: {
			type: Object,
			default: {},
		},
		pokemon: {
			type: Object,
			default: {},
		},
		versionGroups: {
			type: Array,
			default: [],
		},
		showMoveDescriptions: {
			type: Boolean,
			default: true,
		}
	},
	data() {
		return {
			sortColumn: '',
			sortDirection: '',
		};
	},
	computed: {
		colspan() {
			return 6 + this.versionGroups.length + (this.showMoveDescriptions ? 1 : 0);
		},
		visibleMoves() {
			return this.method.moves.filter(m => this.versionGroups.some(vg => m.vgData[vg.identifier]));
		},
	},
	template: `
		<tbody :id="method.identifier + '-moves'">
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
				<th scope="col" class="dex-table__header--sortable dex-pokemon__move-name"
					@click="sortBy('name', 'asc', m => m.name)"
					:class="{
						'dex-table__header--sorted-asc': sortColumn === 'name' && sortDirection === 'asc',
						'dex-table__header--sorted-desc': sortColumn === 'name' && sortDirection === 'desc',
					}"
				>Move</th>
				<th scope="col" class="dex-table__header--sortable"
					@click="sortBy('type', 'asc', m => m.type.name)"
					:class="{
						'dex-table__header--sorted-asc': sortColumn === 'type' && sortDirection === 'asc',
						'dex-table__header--sorted-desc': sortColumn === 'type' && sortDirection === 'desc',
					}"
				>Type</th>
				<th scope="col" class="dex-table__header--sortable"
					@click="sortBy('category', 'asc', m => m.category.name)"
					:class="{
						'dex-table__header--sorted-asc': sortColumn === 'category' && sortDirection === 'asc',
						'dex-table__header--sorted-desc': sortColumn === 'category' && sortDirection === 'desc',
					}"
				>Category</th>
				<th scope="col" class="dex-table__header--sortable"
					@click="sortBy('pp', 'desc', m => m.pp)"
					:class="{
						'dex-table__header--sorted-asc': sortColumn === 'pp' && sortDirection === 'asc',
						'dex-table__header--sorted-desc': sortColumn === 'pp' && sortDirection === 'desc',
					}"
				>PP</th>
				<th scope="col" class="dex-table__header--sortable"
					@click="sortBy('power', 'desc', m => m.power)"
					:class="{
						'dex-table__header--sorted-asc': sortColumn === 'power' && sortDirection === 'asc',
						'dex-table__header--sorted-desc': sortColumn === 'power' && sortDirection === 'desc',
					}"
				>Power</th>
				<th scope="col" class="dex-table__header--sortable"
					@click="sortBy('accuracy', 'desc', m => m.accuracy)"
					:class="{
						'dex-table__header--sorted-asc': sortColumn === 'accuracy' && sortDirection === 'asc',
						'dex-table__header--sorted-desc': sortColumn === 'accuracy' && sortDirection === 'desc',
					}"
				>Accuracy</th>
				<th v-if="showMoveDescriptions" scope="col" class="dex-table__move-description">
					Description
				</th>
			</tr>
			<tr v-for="move in visibleMoves" :key="move.identifier">
				<template v-for="vg in versionGroups" :key="vg.identifier">
					<template v-if="move.vgData[vg.identifier]">
						<template v-if="method.identifier === 'level-up'">
							<td class="dex-table__pokemon-move-data dex-table--number"
								v-tooltip="pokemonMoveTooltip(pokemon, move, vg, method)"
							>
								{{ move.vgData[vg.identifier] }}
							</td>
						</template>

						<template v-else-if="method.identifier === 'machine'">
							<td class="dex-table__pokemon-move-data"
								v-tooltip="pokemonMoveTooltip(pokemon, move, vg, method)"
							>
								{{ move.vgData[vg.identifier] }}
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
				<th scope="row" class="dex-pokemon__move-name">
					<a :href="'/dex/' + generation.identifier + '/moves/' + move.identifier">
						{{ move.name }}
					</a>
				</th>
				<td class="dex-table__move-type">
					<a :href="'/dex/' + generation.identifier + '/types/' + move.type.identifier">
						<img :src="'/images/types/' + move.type.icon" :alt="move.type.name">
					</a>
				</td>
				<td class="dex-table__move-category" v-tooltip="move.category.name">
					<img :src="'/images/categories/' + move.category.icon" :alt="move.category.name">
				</td>
				<td class="dex-table--number">{{ move.pp }}</td>
				<td class="dex-table--number">{{ powerText(move) }}</td>
				<td class="dex-table--number">{{ accuracyText(move) }}</td>
				<td v-if="showMoveDescriptions" class="dex-table__move-description">
					{{ move.description }}
				</td>
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
			this.method.moves.sort((a, b) => {
				const aValue = sortValueCallback(a);
				const bValue = sortValueCallback(b);

				if (aValue < bValue) { return -1 * modifier; }
				if (aValue > bValue) { return +1 * modifier; }
				return 0;
			});
		},
		pokemonMoveTooltip(pokemon, move, vg, method) {
			if (method.identifier === 'level-up') {
				return `${pokemon.name} learns ${move.name} in ${vg.name} at Level ${move.vgData[vg.identifier]}.`;
			}
			if (method.identifier === 'machine') {
				return `${pokemon.name} learns ${move.name} in ${vg.name} at via ${move.vgData[vg.identifier]}.`;
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
		powerText(move) {
			if (move.power === 0) {
				return '—'; // em dash
			}
			if (move.power === 1) {
				return '*';
			}
			return move.power;
		},
		accuracyText(move) {
			if (move.accuracy === 101) {
				return '—'; // em dash
			}
			return move.accuracy + '%';
		},
	},
});
