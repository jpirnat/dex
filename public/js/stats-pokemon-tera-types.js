'use strict';

Vue.component('stats-pokemon-tera-types', {
	props: {
		teraTypes: {
			type: Array,
			default: [],
		},
		month: {
			type: String,
			default: '',
		},
		format: {
			type: String,
			default: '',
		},
		rating: {
			type: Number,
			default: 0,
		},
		pokemon: {
			type: String,
			default: '',
		},
		versionGroup: {
			type: String,
			default: '',
		}
	},
	data() {
		return {
			sortColumn: '',
			sortDirection: '',
		};
	},
	template: `
		<table class="moveset-usage">
			<caption>Tera Types</caption>
			<thead>
				<tr>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('name', 'asc', t => t.name)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'name' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'name' && sortDirection === 'desc',
						}"
					>Type</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('percent', 'desc', t => t.percent)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'percent' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'percent' && sortDirection === 'desc',
						}"
					>%</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="type in teraTypes" :key="type.identifier">
					<td class="dex-table__pokemon-icon">
						<dex-type-link 
							:vg-identifier="versionGroup"
							:type="type"
						></dex-type-link>
					</td>
					<td class="dex-table--number">{{ type.percentText }}</td>
				</tr>
			</tbody>
		</table>
	`,
	methods: {
		sortBy(columnName, defaultDirection, sortValueCallback) {
			if (this.sortColumn !== columnName) {
				// If we're not already sorted by this column, sort in its default direction.
				this.sortColumn = columnName;
				this.sortDirection = defaultDirection;
			} else {
				// If we're already sorted by this column, reverse the direction.
				this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
			}

			const modifier = this.sortDirection === 'asc' ? 1 : -1;

			// Do the sort.
			this.teraTypes.sort((a, b) => {
				const aValue = sortValueCallback(a);
				const bValue = sortValueCallback(b);

				if (aValue < bValue) { return -1 * modifier; }
				if (aValue > bValue) { return +1 * modifier; }
				return 0;
			});
		},
	},
});
