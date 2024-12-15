import DexPagination from './dex-pagination.js';

const { vTooltip } = FloatingVue;
FloatingVue.options.themes.tooltip.delay.show = 0;

export default {
	name: 'dex-abilities-table',
	components: {
		DexPagination,
	},
	directives: {
		tooltip: vTooltip,
	},
	props: {
		abilities: {
			type: Array,
			default: [],
		},
		versionGroup: {
			type: Object,
			default: {},
		},
		filterName: {
			type: String,
			default: '',
		},
		filterDescription: {
			type: String,
			default: '',
		},
	},
	emits: ['update:filterName', 'update:filterDescription'],
	data() {
		return {
			currentPage: 1,
			itemsPerPage: 10,

			sortColumn: '',
			sortDirection: '',
		};
	},
	computed: {
		filteredAbilities() {
			let filteredAbilities = this.abilities;

			if (this.filterName) {
				filteredAbilities = filteredAbilities.filter(a => a.name.toLowerCase().includes(
					this.filterName.toLowerCase()
				));
			}

			if (this.filterDescription) {
				filteredAbilities = filteredAbilities.filter(a => a.description.toLowerCase().includes(
					this.filterDescription.toLowerCase()
				));
			}

			return filteredAbilities;
		},
		paginatedAbilities() {
			const start = (this.currentPage - 1) * this.itemsPerPage;
			const end = start + this.itemsPerPage;
			return this.filteredAbilities.slice(start, end);
		},
	},
	template: `
		<div>
			<dex-pagination
				v-model:current-page="currentPage"
				:number-of-items="filteredAbilities.length"
				:items-per-page="itemsPerPage"
			></dex-pagination>

			<div class="dex-abilities__filters">
				<label class="dex-abilities__filter">
					Filter by ability name: <input type="search" :value="filterName" @input="$emit('update:filterName', $event.target.value)">
				</label>
				<label class="dex-abilities__filter">
					Filter by description: <input type="search" :value="filterDescription" @input="$emit('update:filterDescription', $event.target.value)">
				</label>
			</div>

			<table class="dex-table dex-table--full-width">
				<thead>
					<tr>
						<th scope="col" class="dex-table__ability-name dex-table__header--sortable"
							@click="sortBy('name', 'asc', m => m.name)"
							:class="{
								'dex-table__header--sorted-asc': sortColumn === 'name' && sortDirection === 'asc',
								'dex-table__header--sorted-desc': sortColumn === 'name' && sortDirection === 'desc',
							}"
						>Name</th>
						<th scope="col" class="dex-table__ability-description">Description</th>
						<th scope="col" class="dex-table__ability-pokemon">Pokémon</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="ability in paginatedAbilities">
						<th scope="row" class="dex-table__ability-name">
							<a :href="'/dex/' + versionGroup.identifier + '/abilities/' + ability.identifier">
								{{ ability.name }}
							</a>
						</th>
						<td class="dex-table__ability-description">{{ ability.description }}</td>
						<td class="dex-table__ability-pokemon">
							<a v-for="p in ability.pokemon" class="dex-pokemon-icon-link" :href="'/dex/' + versionGroup.identifier + '/pokemon/' + p.identifier">
								<img class="dex-pokemon-icon" :src="'/images/pokemon/icons/' + p.icon" :alt="p.name" v-tooltip="p.name">
							</a>
						</td>
					</tr>
				</tbody>
			</table>

			<dex-pagination
				v-model:current-page="currentPage"
				:number-of-items="filteredAbilities.length"
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
			this.abilities.sort((a, b) => {
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
		filterDescription() {
			this.currentPage = 1;
		},
	},
};
