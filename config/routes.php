<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
declare(strict_types=1);

use Jp\Dex\Application\Controllers\IndexController;
use Jp\Dex\Application\Middleware\MiddlewareGroups;
use Jp\Dex\Presentation\IndexView;

// Common route parameter definitions.
$abilityIdentifier = '{abilityIdentifier:[-\w]+}';
$itemIdentifier = '{itemIdentifier:[-\w]+}';
$moveIdentifier = '{moveIdentifier:[-\w]+}';
$pokemonIdentifier = '{pokemonIdentifier:[-\w]+}';
$typeIdentifier = '{typeIdentifier:[-\w]+}';
$vgIdentifier = '{vgIdentifier:[-\w]+}';
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

	['GET', "/dex/$vgIdentifier/abilities", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexAbilities',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$vgIdentifier/abilities", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexAbilitiesController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexAbilitiesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$vgIdentifier/abilities/$abilityIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexAbility',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$vgIdentifier/abilities/$abilityIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexAbilityController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexAbilityView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$vgIdentifier/moves", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexMoves',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$vgIdentifier/moves", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexMovesController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexMovesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$vgIdentifier/moves/$moveIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexMove',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$vgIdentifier/moves/$moveIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexMoveController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexMoveView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$vgIdentifier/natures", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexNatures',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$vgIdentifier/natures", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexNaturesController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexNaturesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$vgIdentifier/pokemon", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexPokemons',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$vgIdentifier/pokemon", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexPokemonsController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexPokemonsView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$vgIdentifier/pokemon/$pokemonIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexPokemon',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$vgIdentifier/pokemon/$pokemonIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexPokemonController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexPokemonView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$vgIdentifier/types", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexTypes',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$vgIdentifier/types", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexTypesController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexTypesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$vgIdentifier/types/$typeIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'dexType',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$vgIdentifier/types/$typeIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\DexTypeController::class,
		'controllerMethod' => 'index',
		'viewClass' => \Jp\Dex\Presentation\DexTypeView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/dex/$vgIdentifier/pokemon/$pokemonIdentifier/breeding/$moveIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'breedingChains',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/dex/$vgIdentifier/pokemon/$pokemonIdentifier/breeding/$moveIdentifier", [
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
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'averagedUsage',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/stats/$start-to-$end/$formatIdentifier/$rating", [
		'controllerClass' => \Jp\Dex\Application\Controllers\AveragedUsageController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\AveragedUsageView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/stats/$start-to-$end/$formatIdentifier/$rating/leads", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'averagedLeads',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/stats/$start-to-$end/$formatIdentifier/$rating/leads", [
		'controllerClass' => \Jp\Dex\Application\Controllers\AveragedLeadsController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\AveragedLeadsView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::JSON,
	]],

	['GET', "/stats/$start-to-$end/$formatIdentifier/$rating/pokemon/$pokemonIdentifier", [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'averagedPokemon',
		'middlewareClasses' => MiddlewareGroups::HTML,
	]],

	['GET', "/data/stats/$start-to-$end/$formatIdentifier/$rating/pokemon/$pokemonIdentifier", [
		'controllerClass' => \Jp\Dex\Application\Controllers\AveragedPokemonController::class,
		'controllerMethod' => 'setData',
		'viewClass' => \Jp\Dex\Presentation\AveragedPokemonView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => MiddlewareGroups::JSON,
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
