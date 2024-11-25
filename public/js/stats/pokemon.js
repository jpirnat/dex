const { createApp } = Vue;

import DexBreadcrumbs from '../dex-breadcrumbs.js';
import DexTypeLink from '../dex-type-link.js';
import StatsPokemonAbilities from '../stats-pokemon-abilities.js';
import StatsPokemonItems from '../stats-pokemon-items.js';
import StatsPokemonSpreads from '../stats-pokemon-spreads.js';
import StatsPokemonMoves from '../stats-pokemon-moves.js';
import StatsPokemonTeraTypes from '../stats-pokemon-tera-types.js';
import StatsPokemonTeammates from '../stats-pokemon-teammates.js';
import StatsPokemonCounters from '../stats-pokemon-counters.js';
import DexChartDrawer from '../dex-chart-drawer.js';


const { vTooltip } = FloatingVue;
FloatingVue.options.themes.tooltip.delay.show = 0;

const app = createApp({
	components: {
		DexBreadcrumbs,
		DexTypeLink,
		StatsPokemonAbilities,
		StatsPokemonItems,
		StatsPokemonSpreads,
		StatsPokemonMoves,
		StatsPokemonTeraTypes,
		StatsPokemonTeammates,
		StatsPokemonCounters,
		DexChartDrawer,
	},
	directives: {
		tooltip: vTooltip,
	},
	data() {
		return {
			loading: true,
			loaded: false,

			format: {},
			rating: 0,
			pokemon: {},

			breadcrumbs: [],
			prevMonth: {},
			thisMonth: {},
			nextMonth: {},
			ratings: [],
			prevRank: {},
			thisRank: {},
			nextRank: {},

			versionGroup: {},
			generation: {},
			stats: [],
			rawCount: null,
			averageWeight: null,
			viabilityCeiling: null,

			showAbilities: false,
			showItems: false,
			showTeraTypes: false,
			abilities: [],
			items: [],
			spreads: [],
			moves: [],
			teraTypes: [],
			teammates: [],
			counters: [],

			months: [],

			start: '',
			end: '',
		};
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

				this.format = data.format;
				this.rating = data.rating;
				this.pokemon = data.pokemon;

				this.breadcrumbs = data.breadcrumbs;
				this.prevMonth = data.prevMonth;
				this.thisMonth = data.thisMonth;
				this.nextMonth = data.nextMonth;
				this.ratings = data.ratings;
				this.prevRank = data.prevRank;
				this.thisRank = data.thisRank;
				this.nextRank = data.nextRank;

				this.versionGroup = data.versionGroup;
				this.generation = data.generation;
				this.stats = data.stats;
				this.rawCount = data.rawCount;
				this.averageWeight = data.averageWeight;
				this.viabilityCeiling = data.viabilityCeiling;

				this.showAbilities = data.showAbilities;
				this.showItems = data.showItems;
				this.showTeraTypes = data.showTeraTypes;
				this.abilities = data.abilities;
				this.items = data.items;
				this.spreads = data.spreads;
				this.moves = data.moves;
				this.teraTypes = data.teraTypes;
				this.teammates = data.teammates;
				this.counters = data.counters;

				this.months = data.months;

				this.start = data.thisMonth.value;
				this.end = data.thisMonth.value;

				document.title = data.title;
			}
		});
	},
	methods: {
		addChartLine(line) {
			this.$refs.chart.addLine(line);
		},
		goToAveraged() {
			window.location.assign(`/stats/${this.start}-to-${this.end}/${this.format.identifier}/${this.rating}/pokemon/${this.pokemon.identifier}`);
		},
	},
});

app.mount('#app');
