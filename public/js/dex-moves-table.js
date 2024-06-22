'use strict';

Vue.component('dex-moves-table', {
	props: {
		moves: {
			type: Array,
			default: [],
		},
		versionGroup: {
			type: Object,
			default: {},
		},
		showDescriptions: {
			type: Boolean,
			default: true,
		},
	},
	data() {
		return {
			filterName: '',
			filterDescription: '',

			currentPage: 1,
			itemsPerPage: 20,

			sortColumn: '',
			sortDirection: '',
		};
	},
	computed: {
		filteredMoves() {
			let filteredMoves = this.moves;

			if (this.filterName) {
				filteredMoves = filteredMoves.filter(a => a.name.toLowerCase().includes(
					this.filterName.toLowerCase()
				));
			}

			if (this.filterDescription) {
				filteredMoves = filteredMoves.filter(a => a.description.toLowerCase().includes(
					this.filterDescription.toLowerCase()
				));
			};

			return filteredMoves;
		},
		paginatedMoves() {
			const start = (this.currentPage - 1) * this.itemsPerPage;
			const end = start + this.itemsPerPage;
			return this.filteredMoves.slice(start, end);
		},
	},
	template: `
		<div>
			<dex-pagination
				v-model="currentPage"
				:number-of-items="filteredMoves.length"
				:items-per-page="itemsPerPage"
			></dex-pagination>

			<div class="dex-moves__filters">
				<label class="dex-moves__filter">
					Filter by move name: <input type="search" v-model="filterName">
				</label>
				<label v-if="showDescriptions" class="dex-moves__filter">
					Filter by description: <input type="search" v-model="filterDescription">
				</label>
			</div>

			<table class="dex-table dex-table--full-width">
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
						<th v-if="showDescriptions" scope="col" class="dex-table__move-description">Description</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="move in paginatedMoves" :key="move.identifier">
						<td>
							<a :href="'/dex/' + versionGroup.identifier + '/moves/' + move.identifier">
								{{ move.name }}
							</a>
						</td>
						<td class="dex-table__move-type">
							<dex-type-link
								:vgIdentifier="versionGroup.identifier"
								:type="move.type"
							></dex-type-link>
						</td>
						<td class="dex-table__move-category" v-tooltip="move.category.name">
							<img :src="'/images/categories/' + move.category.icon" :alt="move.category.name">
						</td>
						<td class="dex-table--number">{{ move.pp }}</td>
						<td class="dex-table--number">{{ powerText(move) }}</td>
						<td class="dex-table--number">{{ accuracyText(move) }}</td>
						<td v-if="showDescriptions" class="dex-table__move-description">{{ move.description }}</td>
					</tr>
				</tbody>
			</table>

			<dex-pagination
				v-model="currentPage"
				:number-of-items="filteredMoves.length"
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
	},
	watch: {
		filterName() {
			this.currentPage = 1;
		},
		filterDescription() {
			this.currentPage = 1;
		},
	},
});
