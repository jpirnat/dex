'use strict';

const app = new Vue({
	el: '#app',
	data: {
		loading: true,
		loaded: false,

		breadcrumbs: [],
		pokemon: {},
		move: {},
		chains: [],
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
				this.pokemon = data.pokemon;
				this.move = data.move;
				this.chains = data.chains;

				document.title = data.title;
			}
		});
	},
	methods: {
		toggleChain(chain) {
			chain.show = !chain.show;
		},
	},
});
