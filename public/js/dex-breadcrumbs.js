export default {
	name: 'dex-breadcrumbs',
	props: {
		breadcrumbs: {
			type: Array,
			required: true,
		},
	},
	template: `
		<nav v-if="breadcrumbs.length" class="breadcrumbs" aria-label="Breadcrumb">
			<a class="breadcrumbs__item" href="/">Home</a>
			<template v-for="(b, bIndex) in breadcrumbs">
				<span class="breadcrumbs__separator">»</span>
				<a v-if="b.url" class="breadcrumbs__item" :href="b.url">{{ b.text }}</a>
				<span v-else class="breadcrumbs__item"
					:aria-current="bIndex + 1 === breadcrumbs.length ? 'page' : null"
				>{{ b.text }}</span>
			</template>
		</nav>
	`,
};
