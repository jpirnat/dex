const { vTooltip } = FloatingVue;
FloatingVue.options.themes.tooltip.delay.show = 0;

export default {
	name: 'stats-pokemon-teammates',
	directives: {
		tooltip: vTooltip,
	},
	props: {
		teammates: {
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
			<caption>Teammates</caption>
			<thead>
				<tr>
					<th></th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('name', 'asc', t => t.name)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'name' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'name' && sortDirection === 'desc',
						}"
					>Teammate</th>
					<th scope="col" class="dex-table__header--sortable"
						@click="sortBy('percent', 'desc', t => t.percent)"
						:class="{
							'dex-table__header--sorted-asc': sortColumn === 'percent' && sortDirection === 'asc',
							'dex-table__header--sorted-desc': sortColumn === 'percent' && sortDirection === 'desc',
						}"
						v-tooltip="'X% of teams that use ' + pokemon + ' also use this Pokémon.'"
					>
						<abbr class="dex--tooltip">%</abbr>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="teammate in teammates" :key="teammate.identifier">
					<td class="dex-table__pokemon-icon">
						<img v-if="teammate.icon" class="dex-pokemon-icon" :src="'/images/pokemon/icons/' + teammate.icon" alt="">
					</td>
					<td>
						<a :href="'/stats/' + month + '/' + format + '/' + rating + '/pokemon/' + teammate.identifier">
							{{ teammate.name }}
						</a>
					</td>
					<td class="dex-table--number">{{ teammate.percentText }}</td>
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
			this.teammates.sort((a, b) => {
				const aValue = sortValueCallback(a);
				const bValue = sortValueCallback(b);

				if (aValue < bValue) { return -1 * modifier; }
				if (aValue > bValue) { return +1 * modifier; }
				return 0;
			});
		},
	},
};
