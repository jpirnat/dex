'use strict';

Vue.component('dex-chart-drawer', {
	props: {
		lines: {
			type: Array,
			default: [],
		},
		ratings: {
			type: Array,
			default: [],
		},
	},
	data() {
		return {
			isVisible: false,
			chart: null,
			chartTitle: "Loading...",
			responseLines: [],
			locale: 'en',
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
					}
				}),
			};
		},
		chartOptions() {
			return {
				title: {
					display: true,
					text: this.chartTitle,
					fontSize: 16,
				},
				scales: {
					xAxes: [{
						type: 'time',
						time: {
							unit: 'month',
						},
					}],
					yAxes: [{
						ticks: {
							beginAtZero: true
						},
					}],
				},
				tooltips: {
					mode: 'nearest',
					intersect: false,
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
				<div class="dex-chart__container">
					<canvas id="dex-chart__canvas"></canvas>
				</div>
				<div class="buttons-control">
					<a :href="chartUrl" target="_blank">Save this chart</a>
				</div>
				<div class="buttons-control" v-if="responseLines.length === 1">
					<button role="button" @click="otherRatings">See at all Rating levels</button>
				</div>
				<div class="buttons-control">
					<button role="button" @click="isVisible = false">Close chart panel</button>
				</div>
			</div>
			<div class="dex-drawer__overlay" @click="isVisible = false"></div>
		</div>
	`,
	mounted() {
		this.renderChart();
	},
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
		otherRatings() {
			// TODO: Clean this up. It's only here right now as a fun proof of concept.
			if (this.lines.length !== 1) {
				return;
			}

			const line = this.lines[0];
			this.lines = [];
			this.ratings.forEach(r => {
				const newLine = JSON.parse(JSON.stringify(line));
				newLine.rating = r;
				this.lines.push(newLine);
			});
		}
	},
	watch: {
		lines() {
			this.isVisible = true;

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
				const data = response.data;

				this.chartTitle = data.chartTitle;
				this.responseLines = data.lines;
				this.locale = data.locale;

				await this.$nextTick();
				this.renderChart();
			});
		},
	},
});
