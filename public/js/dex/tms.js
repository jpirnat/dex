const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';
import DexTypeLink from '../dex-type-link.js';

const { vTooltip } = FloatingVue;
FloatingVue.options.themes.tooltip.delay.show = 0;

const app = createApp({
	components: {
		DexBreadcrumbs,
		DexTypeLink,
	},
	directives: {
		tooltip: vTooltip,
	},
	data() {
		return {
			loading: true,
			loaded: false,

			versionGroup: {},
			breadcrumbs: [],
			versionGroups: [],
			machines: [],

			filterItemName: '',
			filterMoveName: '',
			filterMoveDescription: '',

			sortColumn: '',
			sortDirection: '',
		};
	},
	computed: {
		filteredMachines() {
			let filteredMachines = this.machines;

			if (this.filterItemName) {
				filteredMachines = filteredMachines.filter(m => m.item.name.toLowerCase().includes(
					this.filterItemName.toLowerCase()
				));
			}

			if (this.filterMoveName) {
				filteredMachines = filteredMachines.filter(m => m.move.name.toLowerCase().includes(
					this.filterMoveName.toLowerCase()
				));
			}

			if (this.filterMoveDescription) {
				filteredMachines = filteredMachines.filter(m => m.move.description.toLowerCase().includes(
					this.filterMoveDescription.toLowerCase()
				));
			}

			return filteredMachines;
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
				this.versionGroup = data.versionGroup;
				this.breadcrumbs = data.breadcrumbs;
				this.versionGroups = data.versionGroups;
				this.machines = data.machines;
			}
		});
	},
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
			this.machines.sort((a, b) => {
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
});

app.mount('#app');
