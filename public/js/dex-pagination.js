'use strict';

Vue.component('v-popover', VTooltip.VPopover);

Vue.component('dex-pagination', {
	props: {
		value: {
			type: Number,
			required: true,
		},
		numberOfItems: {
			type: Number,
			required: true,
		},
		itemsPerPage: {
			type: Number,
			default: 20,
		},
	},
	data: function() {
		return {
			inputPage: this.value,
		};
	},
	computed: {
		numberOfPages() {
			return Math.ceil(this.numberOfItems / this.itemsPerPage);
		},
		visiblePages() {
			const currentPage = this.value;

			// TODO: This is a terrible hack to get around my lack of knowledge on Vue.js's
			// internal reactivity system.
			this.inputPage = currentPage;

			const visiblePages = [];

			const first = 1;
			const last = this.numberOfPages;

			const numberOfVisiblePages = 9; // Includes the ellipsis boxes.

			// If there are few enough pages, just show them all.
			if (this.numberOfPages <= numberOfVisiblePages) {
				for (let page = first; page <= last; page++) {
					visiblePages.push({ number: page });
				}
				return visiblePages;
			}

			// If we're in the beginning section, show pages from first through (number of
			// visible pages, minus 1 for the ellipsis box).
			if (currentPage <= numberOfVisiblePages - 2) {
				for (let page = first; page <= numberOfVisiblePages - 1; page++) {
					visiblePages.push({ number: page });
				}
				visiblePages.push({ gap: true });
				return visiblePages;
			}

			// If we're in the end section, show pages from (last minus
			if (currentPage >= last - numberOfVisiblePages + 3) {
				visiblePages.push({ gap: true });
				for (let page = last - numberOfVisiblePages + 2; page <= last; page++) {
					visiblePages.push({ number: page });
				}
				return visiblePages;
			}

			// We're in the middle section. Show pages from (current page - 3) through
			// (current page + 3).
			const leftLimit = currentPage - 3; // TODO: Math.floor((numberOfVisiblePages - 2) / 2);
			const rightLimit = currentPage + 3; // TODO: Math.floor((numberOfVisiblePages - 2) / 2);
			visiblePages.push({ gap: true });
			for (let page = leftLimit; page <= rightLimit; page++) {
				visiblePages.push({ number: page });
			}
			visiblePages.push({ gap: true });
			return visiblePages;
		},
	},
	template: `
		<nav class="dex-pagination">
			<ol class="dex-pagination__list">
				<li class="dex-pagination__page dex-pagination__page--first"
					:class="{
						'dex-pagination__page--disabled': value === 1,
					}"
					@click="setCurrentPage(1)"
				>
					&laquo;
				</li>
				<li class="dex-pagination__page" @click="setCurrentPage(value - 1)"
					:class="{
						'dex-pagination__page--disabled': value === 1,
					}"
				>
					&lsaquo;
				</li>
				<template v-for="page in visiblePages">
					<li v-if="page.number" class="dex-pagination__page" @click="setCurrentPage(page.number)"
						:class="{
							'dex-pagination__page--current': value === page.number,
						}"
					>
						{{ page.number }}
					</li>
					<v-popover v-if="page.gap"
						:popover-inner-class="['tooltip-inner', 'popover-inner', 'dex-pagination__popover-inner']"
						:popover-arrow-class="['tooltip-arrow', 'popover-arrow', 'dex-pagination__popover-arrow']"
					>
						<li class="dex-pagination__page">...</li>
						<template #popover>
							<label>
								Go to page:
								<input type="number" min="1" :max="numberOfPages" step="1"
									class="dex-pagination__input"
									v-model.number="inputPage"
									@change="setCurrentPage(inputPage)"
								>
							</label>
						</template>
					</v-popover>
				</template>
				<li class="dex-pagination__page" @click="setCurrentPage(value + 1)"
					:class="{
						'dex-pagination__page--disabled': value === numberOfPages,
					}"
				>
					&rsaquo;
				</li>
				<li class="dex-pagination__page dex-pagination__page--last"
					:class="{
						'dex-pagination__page--disabled': value === numberOfPages,
					}"
					@click="setCurrentPage(numberOfPages)"
				>
					&raquo;
				</li>
			</ol>
		</nav>
	`,
	methods: {
		setCurrentPage(page) {
			page = Math.min(Math.max(1, page), this.numberOfPages);

			this.value = page;
			this.inputPage = this.value;

			this.$emit('input', this.value);
		},
	},
});
