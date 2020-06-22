Vue.component('dex-breadcrumbs', {
	props: {
		breadcrumbs: Array,
		required: true
	},
	template: `
		<nav v-if="breadcrumbs.length" class="breadcrumbs" aria-label="Breadcrumb">
			<ol>
				<li><a class="breadcrumbs__item" href="/">Home</a></li>
				<li v-for="(b, bIndex) in breadcrumbs">
					<a v-if="b.url" class="breadcrumbs__item" :href="b.url">{{ b.text }}</a>
					<span v-else class="breadcrumbs__item"
						:aria-current="bIndex + 1 === breadcrumbs.length ? 'page' : null"
					>{{ b.text }}</span>
				</li>
			</ol>
		</nav>
	`,
});
