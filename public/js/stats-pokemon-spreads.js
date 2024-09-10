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
							<template v-for="(stat, statIndex) in stats" :key="stat.identifier">
								<abbr v-tooltip="stat.name" class="dex-spread--ev dex--tooltip">{{ stat.abbreviation }}</abbr>
								<span v-if="statIndex < stats.length - 1" class="dex-spread--slash">/</span>
							</template>
						</div>
					</th>
					<th>%</th>
					<th>
						<div>Stats</div>
						<div class="dex-spreads__stat-names">
							<template v-for="(stat, statIndex) in stats" :key="stat.identifier">
								<abbr v-tooltip="stat.name" class="dex-spread--stat dex--tooltip">{{ stat.abbreviation }}</abbr>
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
						<template v-for="(stat, statIndex) in stats" :key="stat.identifier">
							<span class="dex-spread--ev"
								:class="{
									'dex-nature--increased': spread.increasedStat === stat.identifier,
									'dex-nature--decreased': spread.decreasedStat === stat.identifier,
								}"
							>
								<template v-if="spread.increasedStat === stat.identifier">+</template><template v-if="spread.decreasedStat === stat.identifier">-</template>{{ spread.evs[stat.identifier] }}
							</span>
							<span v-if="statIndex < stats.length - 1" class="dex-spread--slash">/</span>
						</template>
					</td>
					</td>
					<td class="dex-table--number">
						{{ spread.percent }}
					</td>
					<td>
						<template v-for="(stat, statIndex) in stats" :key="stat.identifier">
							<span class="dex-spread--stat">
								{{ spread.stats[stat.identifier] }}
							</span>
							<span v-if="statIndex < stats.length - 1" class="dex-spread--slash">/</span>
						</template>
					</td>
				</tr>
			</tbody>
		</table>
	`,
});
