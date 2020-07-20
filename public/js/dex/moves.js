'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		breadcrumbs: [],
		generation: {},
		generations: [],
		showMoveDescriptions: true,
		moves: [],

		filterName: '',
		filterDescription: '',

		currentPage: 1,
		itemsPerPage: 10,

		sortColumn: '',
		sortDirection: '',
		sortFunction: null,
	},
	computed: {
		filteredMoves() {
			let filteredMoves = this.moves;

			if (this.filterName) {
				filteredMoves = filteredMoves.filter(a => a.name.toLowerCase().includes(
					this.filterName.toLowerCase())
				);
			}

			if (this.filterDescription) {
				filteredMoves = filteredMoves.filter(a => a.description.toLowerCase().includes(
					this.filterDescription.toLowerCase())
				);
			};

			return filteredMoves;
		},
		paginatedMoves() {
			const start = (this.currentPage - 1) * this.itemsPerPage;
			const end = start + this.itemsPerPage;
			return this.filteredMoves.slice(start, end);
		},
	},
	created() {
		const url = new URL(window.location);

		fetch('/data' + url.pathname, {
			credentials: 'same-origin'
		})
		.then(response => response.json())
		.then(response => {
			this.loading = false;
			this.loaded = true;

			if (response.data) {
				const data = response.data;
				this.breadcrumbs = data.breadcrumbs;
				this.generation = data.generation;
				this.generations = data.generations;
				this.showMoveDescriptions = data.showMoveDescriptions;
				this.moves = data.moves;
			}
		});
	},
	methods: {
		sortBy(column, defaultDirection, sortFunction) {
			// Some of the values we want to sort on for the moves page are in
			// nested objects, so for those cases we're taking sortFunction as
			// a callback to extract the value we want to sort on.

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
				if (sortFunction) {
					const aValue = sortFunction(a);
					const bValue = sortFunction(b);

					if (aValue < bValue) { return -1 * modifier; }
					if (aValue > bValue) { return +1 * modifier; }
					return 0;
				}

				if (a[column] < b[column]) { return -1 * modifier; }
				if (a[column] > b[column]) { return +1 * modifier; }
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
});
