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
		visibleMethods() {
			if (this.showTransferMoves) {
				return this.methods;
			}

			return this.methods.filter(me => {
				return this.visibleVersionGroups.some(vg => me.moves.some(mo => vg.identifier in mo.vgData));
			});
		},
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

			<nav class="dex-move__methods-nav">
				<div>{{ pokemon.name }} can learn moves in the following ways:</div>
				<ul class="dex-move__method-links">
					<li v-for="method in visibleMethods" :key="method.identifier">
						<a :href="'#' + method.identifier + '-moves'" class="dex-link">{{ method.name }}</a>
					</li>
				</ul>
			</nav>

			<div v-if="versionGroup.hasMoveDescriptions" class="dex-pokemon__show-move-descriptions">
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
		toggleMoveDescriptions() {
			this.showMoveDescriptions = !this.showMoveDescriptions;
			window.localStorage.setItem('dexPokemonShowMoveDescriptions', this.showMoveDescriptions);
		},
	},
});
