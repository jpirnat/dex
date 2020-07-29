'use strict';

Vue.component('stats-pokemon-items', {
	props: {
		items: {
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
						@click="sortBy('name', 'asc', i => i.name)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'name' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'name' && sortDirection === 'desc',
						}"
					>Item</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('percent', 'desc', i => i.percent)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'percent' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'percent' && sortDirection === 'desc',
						}"
					>%</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('change', 'desc', i => i.change)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'change' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'change' && sortDirection === 'desc',
						}"
					>Δ</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="item in items" :key="item.identifier">
					<td>
						<a :href="'/stats/' + month + '/' + format + '/' + rating + '/items/' + item.identifier">
							{{ item.name }}
						</a>
					</td>
					<td class="dex-table--number">{{ item.percentText }}</td>
					<td class="dex-table--number chart-link"
						:class="{
							'dex-table--percent-plus': item.change > 0,
							'dex-table--percent-minus': item.change < 0,
						}"
						@click="addChartLine(item)"
					>
						<div class="chart-link__inner">
							{{ item.changeText }}
							<img class="chart-link__icon" src="/images/porydex/chart-icon.png">
						</div>
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
			this.items.sort((a, b) => {
				const aValue = sortValueCallback(a);
				const bValue = sortValueCallback(b);

				if (aValue < bValue) { return -1 * modifier; }
				if (aValue > bValue) { return +1 * modifier; }
				return 0;
			});
		},
		addChartLine(item) {
			this.$emit('add-chart-line', {
				type: 'moveset-item',
				format: this.format,
				rating: this.rating,
				pokemon: this.pokemon,
				item: item.identifier
			});
		},
	},
});
