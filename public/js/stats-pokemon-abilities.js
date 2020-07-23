'use strict';

Vue.component('stats-pokemon-abilities', {
	props: {
		abilities: {
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
			<caption>Abilities</caption>
			<thead>
				<tr>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('name', 'asc', a => a.name)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'name' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'name' && sortDirection === 'desc',
						}"
					>Ability</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('percent', 'desc', a => a.percent)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'percent' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'percent' && sortDirection === 'desc',
						}"
					>%</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('change', 'desc', a => a.change)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'change' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'change' && sortDirection === 'desc',
						}"
					>Δ</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="ability in abilities" :key="ability.identifier">
					<td>
						<a :href="'/stats/' + month + '/' + format + '/' + rating + '/abilities/' + ability.identifier">
							{{ ability.name }}
						</a>
					</td>
					<td class="dex-table--number">{{ ability.percentText }}</td>
					<td class="dex-table--number"
						:class="{
							'dex-table--percent-plus': ability.change > 0,
							'dex-table--percent-minus': ability.change < 0,
						}"
					>
						<a class="chart-link"
							:href="'/stats/chart?type=moveset-ability&format=' + format + '&rating=' + rating + '&pokemon=' + pokemon + '&ability=' + ability.identifier"
						>
							{{ ability.changeText }}
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
			this.abilities.sort((a, b) => {
				const aValue = sortValueCallback(a);
				const bValue = sortValueCallback(b);

				if (aValue < bValue) { return -1 * modifier; }
				if (aValue > bValue) { return +1 * modifier; }
				return 0;
			});
		},
	},
});
