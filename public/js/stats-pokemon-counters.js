'use strict';

Vue.component('stats-pokemon-counters', {
	props: {
		counters: {
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
		pokemon: { // name, not identifier!!!
			type: String,
			default: {},
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
			<caption>Checks and Counters</caption>
			<thead>
				<tr>
					<th></th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('name', 'asc', c => c.name)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'name' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'name' && sortDirection === 'desc',
						}"
					>Counter</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('score', 'desc', c => c.score)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'score' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'score' && sortDirection === 'desc',
						}"
						v-tooltip="'The counter\\\'s numeric score, weighted to remove bias towards low-probability matchups (% - 4σ)'"
					>
						<abbr class="dex--tooltip">Score</abbr>
					</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('percent', 'desc', c => c.percent)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'percent' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'percent' && sortDirection === 'desc',
						}"
						v-tooltip="'The percent of encounters where ' + pokemon + ' was knocked out or switched out'"
					>
						<abbr class="dex--tooltip">%</abbr>
					</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('standardDeviation', 'desc', c => c.standardDeviation)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'standardDeviation' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'standardDeviation' && sortDirection === 'desc',
						}"
						v-tooltip="'The standard deviation of the percent of encounters where ' + pokemon + ' was knocked out or switched out'"
					>
						<abbr class="dex--tooltip">σ</abbr>
					</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('percentKnockedOut', 'desc', c => c.percentKnockedOut)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'percentKnockedOut' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'percentKnockedOut' && sortDirection === 'desc',
						}"
					>% KOed</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('percentSwitchedOut', 'desc', c => c.percentSwitchedOut)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'percentSwitchedOut' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'percentSwitchedOut' && sortDirection === 'desc',
						}"
					>% switched out</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="counter in counters" :key="counter.identifier">
					<td class="dex-table__pokemon-icon">
						<img v-if="counter.icon" class="dex-pokemon-icon" :src="'/images/pokemon/icons/' + counter.icon" alt="">
					</td>
					<td>
						<a :href="'/stats/' + month + '/' + format + '/' + rating + '/pokemon/' + counter.identifier">
							{{ counter.name }}
						</a>
					</td>
					<td class="dex-table--number">{{ counter.scoreText }}</td>
					<td class="dex-table--number">{{ counter.percentText }}</td>
					<td class="dex-table--number">{{ counter.standardDeviationText }}</td>
					<td class="dex-table--number">{{ counter.percentKnockedOutText }}</td>
					<td class="dex-table--number">{{ counter.percentSwitchedOutText }}</td>
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
			this.counters.sort((a, b) => {
				const aValue = sortValueCallback(a);
				const bValue = sortValueCallback(b);

				if (aValue < bValue) { return -1 * modifier; }
				if (aValue > bValue) { return +1 * modifier; }
				return 0;
			});
		},
	},
});
