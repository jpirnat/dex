const { Dropdown } = FloatingVue;

export default {
	name: 'dex-pagination',
	components: {
		Dropdown,
	},
	props: {
		currentPage: {
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
	emits: ['update:currentPage'],
	data() {
		return {
			inputPage: this.currentPage,
		};
	},
	computed: {
		numberOfPages() {
			return Math.ceil(this.numberOfItems / this.itemsPerPage);
		},
		visiblePages() {
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

			// If we're in the beginning section, show pages from first through
			// [number of visible pages, minus 1 for the ellipsis box].
			if (this.currentPage <= numberOfVisiblePages - 2) {
				for (let page = first; page <= numberOfVisiblePages - 1; page++) {
					visiblePages.push({ number: page });
				}
				visiblePages.push({ gap: true });
				return visiblePages;
			}

			// If we're in the end section, show pages from [last minus number
			// of visible pages] through last.
			if (this.currentPage >= last - numberOfVisiblePages + 3) {
				visiblePages.push({ gap: true });
				for (let page = last - numberOfVisiblePages + 2; page <= last; page++) {
					visiblePages.push({ number: page });
				}
				return visiblePages;
			}

			// We're in the middle section. Show pages from (current page - 3) through
			// (current page + 3).
			const leftLimit = this.currentPage - 3;
			const rightLimit = this.currentPage + 3;
			visiblePages.push({ gap: true });
			for (let page = leftLimit; page <= rightLimit; page++) {
				visiblePages.push({ number: page });
			}
			visiblePages.push({ gap: true });
			return visiblePages;
		},
	},
	template: `
		<nav class="dex-pagination" v-if="numberOfItems > itemsPerPage">
			<ol class="dex-pagination__list">
				<li class="dex-pagination__page dex-pagination__page--first"
					:class="{
						'dex-pagination__page--disabled': currentPage === 1,
					}"
					@click="setCurrentPage(1)"
				>
					&laquo;
				</li>
				<li class="dex-pagination__page" @click="setCurrentPage(currentPage - 1)"
					:class="{
						'dex-pagination__page--disabled': currentPage === 1,
					}"
				>
					&lsaquo;
				</li>
				<template v-for="page in visiblePages">
					<li v-if="page.number" class="dex-pagination__page" @click="setCurrentPage(page.number)"
						:class="{
							'dex-pagination__page--current': currentPage === page.number,
						}"
					>
						{{ page.number }}
					</li>
					<dropdown v-if="page.gap"
						:popper-class="['dex-pagination__popper']"
					>
						<li class="dex-pagination__page">...</li>
						<template #popper>
							<label class="dex-pagination__label">
								<span>Go to page:</span>
								<input type="number" min="1" :max="numberOfPages" step="1"
									class="dex-pagination__input"
									v-model.number="inputPage"
									@change="setCurrentPage(inputPage)"
								>
							</label>
						</template>
					</dropdown>
				</template>
				<li class="dex-pagination__page" @click="setCurrentPage(currentPage + 1)"
					:class="{
						'dex-pagination__page--disabled': currentPage === numberOfPages,
					}"
				>
					&rsaquo;
				</li>
				<li class="dex-pagination__page dex-pagination__page--last"
					:class="{
						'dex-pagination__page--disabled': currentPage === numberOfPages,
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

			this.inputPage = page;

			this.$emit('update:currentPage', page);
		},
	},
};
