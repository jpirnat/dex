'use strict';

Vue.component('dex-type-link', {
	props: {
		vgIdentifier: {
			type: String,
			required: true,
		},
		type: {
			type: Object, // Required fields: identifier, icon, name.
			required: true,
		},
	},
	template: `
		<a class="dex-link" :href="'/dex/' + vgIdentifier + '/types/' + type.identifier">
			<img v-if="type.icon" class="dex-type-icon"
				:src="'/images/types/' + type.icon"
				:alt="type.name" v-tooltip="type.name"
			>
			<template v-else>{{ type.name }}</template>
		</a>
	`,
});
