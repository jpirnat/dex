'use strict';

Vue.component('dex-pokemon-moves', {
	props: {
		versionGroup: {
			// Required fields: identifier, generationId, hasMoveDescriptions, TODO
			type: Object,
			default: {},
		},
		pokemon: {
			// Required fields: name, TODO
			type: Object,
			default: {},
		},
		types: {
			// Required fields: identifier, icon, name
			type: Array,
			default: [],
		},
		categories: {
			// Required fields: identifier, icon, name
			type: Array,
			default: [],
		},
		versionGroups: {
			// Required fields: identifier, generationId, TODO
			type: Array,
			default: [],
		},
		methods: {
			// Required fields: identifier, name, moves
			type: Array,
			default: [],
		},
	},
	data() {
		const showTransferMoves = JSON.parse(
			window.localStorage.getItem('dexPokemonShowTransferMoves') ?? 'false'
		);
		const showMoveDescriptions = JSON.parse(
			window.localStorage.getItem('dexPokemonShowMoveDescriptions') ?? 'true'
		);

		return {
			showTransferMoves: showTransferMoves,
			showTypeFilters: false,
			filterTypes: [],
			showCategoryFilters: false,
			filterCategories: [],
			showMoveDescriptions: showMoveDescriptions,
		};
	},
	computed: {
		hasTransferMoves() {
			// The Pokémon has transfer moves if there are multiple generations
			// among the version groups.
			const generationIds = this.versionGroups.map(vg => vg.generationId);
			return new Set(generationIds).size > 1;
		},
		visibleVersionGroups() {
			if (this.showTransferMoves) {
				return this.versionGroups;
			}

			return this.versionGroups.filter(vg => vg.generationId === this.versionGroup.generationId);
		},
		visibleTypes() {
			if (!this.showTypeFilters || this.filterTypes.length === 0) {
				return this.types.map(t => t.identifier);
			}

			return this.filterTypes;
		},
		visibleCategories() {
			if (!this.showCategoryFilters || this.filterCategories.length === 0) {
				return this.categories.map(c => c.identifier);
			}

			return this.filterCategories;
		},
		visibleMethods() {
			if (this.showTransferMoves && !this.showTypeFilters && !this.showCategoryFilters) {
				return this.methods;
			}

			return this.methods.filter(me => {
				return me.moves.some(mo => {
					return this.visibleVersionGroups.some(vg => vg.identifier in mo.vgData)
						&& this.visibleTypes.includes(mo.type.identifier)
						&& this.visibleCategories.includes(mo.category.identifier)
					;
				});
			});
		},
	},
	created() {
		this.types.forEach(t => this.filterTypes.push(t.identifier));
		this.categories.forEach(c => this.filterCategories.push(c.identifier));
	},
	template: `
		<div>
			<h2 class="dex-section__title">Moves</h2>

			<div v-if="hasTransferMoves">
				<label>
					<input type="checkbox" v-model="showTransferMoves" @click="toggleTransferMoves">
					<template v-if="versionGroup.generationId > 1">Show transfer moves</template>
					<template v-else>Show tradeback moves</template>
				</label>
			</div>

			<div>
				<label @click="toggleTypeFilters">
					<template v-if="!showTypeFilters">&#9654;</template>
					<template v-if="showTypeFilters">&#9660;</template>
					Filter by type
				</label>
			</div>
			<div v-if="showTypeFilters">
				<div class="dex-pokemon-moves__filter-types">
					<label v-for="t in types" class="dex-pokemon-moves__filter-type">
						<img class="dex-type-icon" :src="'/images/types/' + t.icon" :alt="t.name" v-tooltip="t.name"
							:class="{
								'dex-pokemon-moves__filter-type--inactive': !filterTypes.includes(t.identifier),
							}"
						>
						<input type="checkbox" class="dex-pokemon-moves__filter-type-input"
							v-model="filterTypes" :value="t.identifier"
						>
					</label>
				</div>
				<div class="dex-pokemon-moves__filter-buttons">
					<button type="button" @click="selectAllTypes">Select All</button>
					<button type="button" @click="unselectAllTypes">Unselect All</button>
				</div>
			</div>

			<div>
				<label @click="toggleCategoryFilters">
					<template v-if="!showCategoryFilters">&#9654;</template>
					<template v-if="showCategoryFilters">&#9660;</template>
					Filter by category
				</label>
			</div>
			<div v-if="showCategoryFilters">
				<div class="dex-pokemon-moves__filter-categories">
					<label v-for="c in categories" class="dex-pokemon-moves__filter-category">
						<img :src="'/images/categories/' + c.icon" :alt="c.name" v-tooltip="c.name"
							:class="{
								'dex-pokemon-moves__filter-category--inactive': !filterCategories.includes(c.identifier),
							}"
						>
						<input type="checkbox" class="dex-pokemon-moves__filter-category-input"
							v-model="filterCategories" :value="c.identifier"
						>
					</label>
				</div>
				<div class="dex-pokemon-moves__filter-buttons">
					<button type="button" @click="selectAllCategories">Select All</button>
					<button type="button" @click="unselectAllCategories">Unselect All</button>
				</div>
			</div>

			<nav class="dex-move__methods-nav">
				<div>{{ pokemon.name }} can learn moves in the following ways:</div>
				<ul class="dex-move__method-links">
					<li v-for="method in visibleMethods" :key="method.identifier">
						<a :href="'#' + method.identifier + '-moves'" class="dex-link">{{ method.name }}</a>
					</li>
				</ul>
			</nav>

			<div v-if="versionGroup.hasMoveDescriptions" class="dex-pokemon-moves__show-descriptions">
				<label>
					<input type="checkbox" v-model="showMoveDescriptions" @click="toggleMoveDescriptions">
					Show move descriptions
				</label>
			</div>

			<table class="dex-table dex-table--full-width">
				<tbody is="dex-pokemon-method-moves"
					v-for="method in visibleMethods" :key="method.identifier"
					:method="method"
					:version-group="versionGroup"
					:pokemon="pokemon"
					:version-groups="visibleVersionGroups"
					:types="visibleTypes"
					:categories="visibleCategories"
					:show-move-descriptions="showMoveDescriptions"
				></tbody>
			</table>
		</div>
	`,
	methods: {
		toggleTransferMoves() {
			this.showTransferMoves = !this.showTransferMoves;
			window.localStorage.setItem('dexPokemonShowTransferMoves', this.showTransferMoves);
		},

		toggleTypeFilters() {
			this.showTypeFilters = !this.showTypeFilters;
		},
		selectAllTypes() {
			this.filterTypes = this.types.map(t => t.identifier);
		},
		unselectAllTypes() {
			this.filterTypes = [];
		},

		toggleCategoryFilters() {
			this.showCategoryFilters = !this.showCategoryFilters;
		},
		selectAllCategories() {
			this.filterCategories = this.categories.map(c => c.identifier);
		},
		unselectAllCategories() {
			this.filterCategories = [];
		},

		toggleMoveDescriptions() {
			this.showMoveDescriptions = !this.showMoveDescriptions;
			window.localStorage.setItem('dexPokemonShowMoveDescriptions', this.showMoveDescriptions);
		},
	},
});
