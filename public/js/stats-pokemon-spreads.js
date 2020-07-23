'use strict';

Vue.component('stats-pokemon-spreads', {
	props: {
		spreads: {
			type: Array,
			default: [],
		},
		stats: {
			type: Array,
			default: [],
		},
	},
	template: `
		<table class="moveset-usage">
			<caption>Spreads</caption>
			<thead>
				<tr>
					<th>Nature</th>
					<th>
						<div>EVs</div>
						<div class="dex-spreads__stat-names">
							<template v-for="(stat, statIndex) in stats" :key="stat.key">
								<abbr :title="stat.name" class="dex-spread--ev">{{ stat.abbr }}</abbr>
								<span v-if="statIndex < stats.length - 1" class="dex-spread--slash">/</span>
							</template>
						</div>
					</th>
					<th>%</th>
					<th>
						<div>Stats</div>
						<div class="dex-spreads__stat-names">
							<template v-for="(stat, statIndex) in stats" :key="stat.key">
								<abbr :title="stat.name" class="dex-spread--stat">{{ stat.abbr }}</abbr>
								<span v-if="statIndex < stats.length - 1" class="dex-spread--slash">/</span>
							</template>
						</div>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="spread in spreads">
					<td>{{ spread.nature }}</td>
					<td>
						<template v-for="(stat, statIndex) in stats" :key="stat.key">
							<span class="dex-spread--ev"
								:class="{
									'dex-nature--increased': spread.increasedStat === stat.key,
									'dex-nature--decreased': spread.decreasedStat === stat.key,
								}"
							>
								<template v-if="spread.increasedStat === stat.key">+</template><template v-if="spread.decreasedStat === stat.key">-</template>{{ spread.evs[stat.key] }}
							</span>
							<span v-if="statIndex < stats.length - 1" class="dex-spread--slash">/</span>
						</template>
					</td>
					</td>
					<td class="dex-table--number">
						{{ spread.percent }}
					</td>
					<td>
						<template v-for="(stat, statIndex) in stats" :key="stat.key">
							<span class="dex-spread--stat">
								{{ spread.stats[stat.key] }}
							</span>
							<span v-if="statIndex < stats.length - 1" class="dex-spread--slash">/</span>
						</template>
					</td>
				</tr>
			</tbody>
		</table>
	`,
});
