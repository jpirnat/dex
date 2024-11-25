const { createApp } = Vue;

const app = createApp({
	data() {
		return {
			loading: true,
			loaded: false,

			chartTitle: '',
			responseLines: [],
			locale: '',
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
	},
	mounted() {
		const url = new URL(window.location);
		const encoded = url.searchParams.get('lines');
		const lines = JSON.parse(decodeURIComponent(encoded));

		fetch('/stats/chart', {
			method: 'POST',
			credentials: 'same-origin',
			headers: new Headers({
				'Content-Type': 'application/json'
			}),
			body: JSON.stringify({
				lines: lines,
			}),
		})
		.then(response => response.json())
		.then(async response => {
			this.loading = false;
			this.loaded = true;

			if (response.data) {
				const data = response.data;

				this.chartTitle = data.chartTitle;
				this.responseLines = data.lines;
				this.locale = data.locale;

				document.title = `Porydex - Stats - ${this.chartTitle}`;

				await this.$nextTick();
				this.renderChart();
			}
		});
	},
	methods: {
		renderChart() {
			let ctx = document.getElementById('dex-chart__canvas').getContext('2d');
			this.chart = new Chart(ctx, {
				type: 'line',
				data: this.chartData,
				options: this.chartOptions,
			});
		},
	},
});

app.mount('#app');
