export default {
	name: 'dex-chart-drawer',
	props: {
		ratings: {
			type: Array,
			default: [],
		},
	},
	data() {
		return {
			loading: true,

			isVisible: false,
			chart: null,
			chartTitle: "Loading...",
			lines: [],
			responseLines: [],
			locale: 'en',
			addingAnotherLine: false,
			seeingAllRatings: false,
		};
	},
	computed: {
		chartData() {
			return {
				datasets: this.responseLines.map(l => {
					return {
						label: l.label,
						data: l.data, // This is already in {x: 'YYYY-MM', y: Number} format.
						borderColor: l.color,
						fill: false,
						tension: 0.4,
					}
				}),
			};
		},
		chartOptions() {
			return {
				scales: {
					x: {
						type: 'time',
						time: {
							unit: 'month',
						},
					},
					y: {
						beginAtZero: true,
					},
				},
				plugins: {
					title: {
						display: true,
						text: this.chartTitle,
						font: {
							size: 16,
						}
					},
					tooltip: {
						mode: 'nearest',
						intersect: false,
						callbacks: {
							title(tooltipItems) {
								// Convert each data point's tooltip title from "YYYY-MM" to "Month Year"
								const d = new Date(tooltipItems[0].raw.x + '-01');
								return d.toLocaleString('en-US', { month: 'long', year: 'numeric' });
							},
						},
					},
				},
			};
		},
		chartUrl() {
			const encoded = encodeURIComponent(JSON.stringify(this.lines))
			return `/stats/chart?lines=${encoded}`;
		},
	},
	template: `
		<div v-show="isVisible">
			<div class="dex-drawer__content">
				<div v-if="loading" class="dex-loader-container">
					<div class="dex-loader"></div>
				</div>
				<template v-if="!loading">
					<div class="dex-chart__container">
						<canvas id="dex-chart__canvas"></canvas>
					</div>
					<div class="buttons-control">
						<a :href="chartUrl" target="_blank">Save this chart</a>
					</div>
					<div class="buttons-control">
						<button role="button" @click="addAnotherLine" v-if="!seeingAllRatings"
							style="margin-right: 10px;"
						>
							Add Another Line
						</button>
						<button role="button" @click="seeAllRatings" v-if="responseLines.length === 1"
						>
							See This at All Rating Levels
						</button>
						<div class="space"></div>
					</div>
					<div class="buttons-control">
						<button role="button" @click="closeAndClear">Close and Clear Chart</button>
					</div>
				</template>
			</div>
			<div class="dex-drawer__overlay" @click="closeAndClear"></div>
		</div>
	`,
	methods: {
		renderChart() {
			if (this.chart) {
				this.chart.destroy();
			}

			let ctx = document.getElementById('dex-chart__canvas').getContext('2d');
			this.chart = new Chart(ctx, {
				type: 'line',
				data: this.chartData,
				options: this.chartOptions,
			});
		},
		addLine(line) {
			if (this.addingAnotherLine) {
				this.lines.push(line);

				return;
			}

			this.lines = [line];
		},
		addAnotherLine() {
			this.addingAnotherLine = true;
			this.seeingAllRatings = false;
			this.isVisible = false;
		},
		seeAllRatings() {
			if (this.lines.length !== 1) {
				return;
			}

			const oldLine = this.lines[0];
			this.lines = [];
			this.ratings.forEach(r => {
				const newLine = Object.assign({}, oldLine);
				newLine.rating = r;
				this.lines.push(newLine);
			});

			this.seeingAllRatings = true;
		},
		closeAndClear() {
			this.addingAnotherLine = false;
			this.seeingAllRatings = false;
			this.isVisible = false;
		},
	},
	watch: {
		lines: {
			deep: 1,
			handler() {
				this.isVisible = true;

				this.loading = true;
				fetch('/stats/chart', {
					method: 'POST',
					credentials: 'same-origin',
					headers: new Headers({
						'Content-Type': 'application/json'
					}),
					body: JSON.stringify({
						lines: this.lines,
					}),
				})
				.then(response => response.json())
				.then(async response => {
					this.loading = false;

					if (response.data) {
						const data = response.data;

						this.chartTitle = data.chartTitle;
						this.responseLines = data.lines;
						this.locale = data.locale;

						await this.$nextTick();
						this.renderChart();
					}
				});
			},
		},
	},
};
