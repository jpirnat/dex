<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
declare(strict_types=1);

use Jp\Dex\Application\Controllers\IndexController;
use Jp\Dex\Application\Middleware\MiddlewareGroups;
use Jp\Dex\Presentation\IndexView;

// Common route parameter definitions.
$abilityIdentifier = '{abilityIdentifier:[-\w]+}';
$generationIdentifier = '{generationIdentifier:[-\w]+}';
$itemIdentifier = '{itemIdentifier:[-\w]+}';
$moveIdentifier = '{moveIdentifier:[-\w]+}';
$pokemonIdentifier = '{pokemonIdentifier:[-\w]+}';
$typeIdentifier = '{typeIdentifier:[-\w]+}';
$versionGroupIdentifier = '{versionGroupIdentifier:[-\w]+}';
$month = '{month:\d{4}-\d{2}}';
$formatIdentifier = '{formatIdentifier:[-\w]+}';
$rating = '{rating:\d+}';
$start = '{start:\d{4}-\d{2}}';
$end = '{end:\d{4}-\d{2}}';

// Route definitions.
$routes = [
	['GET', '/', [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/dex/$generationIdentifier/abilities", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexAbilities',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$generationIdentifier/abilities", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexAbilitiesController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexAbilitiesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$generationIdentifier/abilities/$abilityIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexAbility',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$generationIdentifier/abilities/$abilityIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexAbilityController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexAbilityView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$generationIdentifier/moves", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexMoves',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$generationIdentifier/moves", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexMovesController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexMovesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$generationIdentifier/moves/$moveIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexMove',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$generationIdentifier/moves/$moveIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexMoveController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexMoveView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$generationIdentifier/natures", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexNatures',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$generationIdentifier/natures", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexNaturesController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexNaturesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$generationIdentifier/pokemon", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexPokemons',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$generationIdentifier/pokemon", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexPokemonsController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexPokemonsView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$generationIdentifier/pokemon/$pokemonIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexPokemon',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$generationIdentifier/pokemon/$pokemonIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexPokemonController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexPokemonView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$generationIdentifier/types", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexTypes',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$generationIdentifier/types", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexTypesController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexTypesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$generationIdentifier/types/$typeIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexType',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$generationIdentifier/types/$typeIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexTypeController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexTypeView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$generationIdentifier/pokemon/$pokemonIdentifier/breeding/$moveIdentifier/$versionGroupIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'breedingChains',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$generationIdentifier/pokemon/$pokemonIdentifier/breeding/$moveIdentifier/$versionGroupIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\BreedingChainsController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\BreedingChainsView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', '/stats', [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'statsIndex',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', '/data/stats', [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsIndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\StatsIndexView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/stats/$month", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'statsMonth',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/stats/$month", [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsMonthController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\StatsMonthView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'statsUsage',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/stats/$month/$formatIdentifier/$rating", [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsUsageController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\StatsUsageView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', '/stats/current', [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'statsUsage',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', '/data/stats/current', [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsUsageController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\StatsUsageView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::CURRENT_STATS,
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/leads", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'statsLeads',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/stats/$month/$formatIdentifier/$rating/leads", [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsLeadsController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\StatsLeadsView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/pokemon/$pokemonIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'statsPokemon',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/stats/$month/$formatIdentifier/$rating/pokemon/$pokemonIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsPokemonController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\StatsPokemonView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/abilities/$abilityIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'statsAbility',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/stats/$month/$formatIdentifier/$rating/abilities/$abilityIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsAbilityController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\StatsAbilityView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/items/$itemIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'statsItem',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/stats/$month/$formatIdentifier/$rating/items/$itemIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsItemController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\StatsItemView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/moves/$moveIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'statsMove',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/stats/$month/$formatIdentifier/$rating/moves/$moveIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsMoveController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\StatsMoveView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	// Averaged
	['GET', "/stats/$start-to-$end/$formatIdentifier/$rating", [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsAveragedUsageController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\StatsAveragedUsageView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/stats/$start-to-$end/$formatIdentifier/$rating/leads", [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsAveragedLeadsController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\StatsAveragedLeadsView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/stats/$start-to-$end/$formatIdentifier/$rating/pokemon/$pokemonIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsAveragedPokemonController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\StatsAveragedPokemonView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	// Charts
	['GET', '/stats/chart', [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'statsChart',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['POST', '/stats/chart', [
		'controllerClass' => \Jp\Dex\Application\Controllers\StatsChartController::class,
		'controllerMethod' => 'ajax',
		'viewClass' => \Jp\Dex\Presentation\StatsChartView::class,
		'viewMethod' => 'ajax',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', '/about', [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'about',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	// Errors
	['GET', '/404', [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'error404',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', '/error', [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'error',
		'middlewareClasses' => MiddlewareGroups::ERROR,
	]],
];

// Route dispatching.
return \FastRoute\simpleDispatcher(
	function (\FastRoute\RouteCollector $routeCollector) use ($routes) {
		foreach ($routes as $route) {
			$routeCollector->addRoute(...$route);
		}
	}
);
