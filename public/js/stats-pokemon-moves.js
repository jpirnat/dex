'use strict';

Vue.component('stats-pokemon-moves', {
	props: {
		moves: {
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
	},
	data() {
		return {
			sortColumn: '',
			sortDirection: '',
		};
	},
	template: `
		<table class="moveset-usage">
			<caption>Items</caption>
			<thead>
				<tr>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('name', 'asc', m => m.name)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'name' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'name' && sortDirection === 'desc',
						}"
					>Item</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('percent', 'desc', m => m.percent)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'percent' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'percent' && sortDirection === 'desc',
						}"
					>%</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('change', 'desc', m => m.change)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'change' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'change' && sortDirection === 'desc',
						}"
					>Δ</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="move in moves" :key="move.identifier">
					<td>
						<a :href="'/stats/' + month + '/' + format + '/' + rating + '/moves/' + move.identifier">
							{{ move.name }}
						</a>
					</td>
					<td class="dex-table--number">{{ move.percentText }}</td>
					<td class="dex-table--number"
						:class="{
							'dex-table--percent-plus': move.change > 0,
							'dex-table--percent-minus': move.change < 0,
						}"
					>
						<a class="chart-link"
							:href="'/stats/chart?type=moveset-move&format=' + format + '&rating=' + rating + '&pokemon=' + pokemon + '&move=' + move.identifier"
						>
							{{ move.changeText|e }}
							<img src="/images/porydex/chart-icon.png">
						</a>
					</td>
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
			this.moves.sort((a, b) => {
				const aValue = sortValueCallback(a);
				const bValue = sortValueCallback(b);

				if (aValue < bValue) { return -1 * modifier; }
				if (aValue > bValue) { return +1 * modifier; }
				return 0;
			});
		},
	},
});
