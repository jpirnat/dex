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
		versionGroup: {
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
	computed: {
		showPriority() {
			return this.moves.some(m => m.priority !== 0);
		},
	},
	template: `
		<table class="moveset-usage">
			<caption>Moves</caption>
			<thead>
				<tr>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('name', 'asc', m => m.name)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'name' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'name' && sortDirection === 'desc',
						}"
					>Move</th>
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
					<th scope="col" class="dex-table--number dex-table__header--sortable"
						@click="sortBy('pp', 'desc', m => m.pp)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'pp' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'pp' && sortDirection === 'desc',
						}"
					>PP</th>
					<th scope="col" class="dex-table--number dex-table__header--sortable"
						@click="sortBy('power', 'desc', m => m.power)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'power' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'power' && sortDirection === 'desc',
						}"
					>Power</th>
					<th scope="col" class="dex-table--number dex-table__header--sortable"
						@click="sortBy('accuracy', 'desc', m => m.accuracy)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'accuracy' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'accuracy' && sortDirection === 'desc',
						}"
					>Accuracy</th>
					<th scope="col" class="dex-table--number dex-table__header--sortable" v-if="showPriority"
						@click="sortBy('priority', 'desc', m => m.priority)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'priority' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'priority' && sortDirection === 'desc',
						}"
					>Priority</th>
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
					<td class="dex-table--number chart-link"
						:class="{
							'dex-table--percent-plus': move.change > 0,
							'dex-table--percent-minus': move.change < 0,
						}"
						@click="addChartLine(move)"
					>
						<div class="chart-link__inner">
							{{ move.changeText }}
							<img class="chart-link__icon" src="/images/porydex/chart-icon.png">
						</div>
					</td>
					<td class="dex-table__move-type">
						<dex-type-link
							:vg-identifier="versionGroup"
							:type="move.type"
						></dex-type-link>
					</td>
					<td class="dex-table__move-category" v-tooltip="move.category.name">
						<img :src="'/images/categories/' + move.category.icon" :alt="move.category.name">
					</td>
					<td class="dex-table--number">{{ move.pp }}</td>
					<td class="dex-table--number">{{ powerText(move) }}</td>
					<td class="dex-table--number">{{ accuracyText(move) }}</td>
					<td class="dex-table--number" v-if="showPriority">{{ move.priority }}</td>
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
		addChartLine(move) {
			this.$emit('add-chart-line', {
				type: 'moveset-move',
				format: this.format,
				rating: this.rating,
				pokemon: this.pokemon,
				move: move.identifier
			});
		},
	},
});
