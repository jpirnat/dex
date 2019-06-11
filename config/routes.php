<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
declare(strict_types=1);

use Jp\Dex\Application\Controllers\BreedingChainsController;
use Jp\Dex\Application\Controllers\DexAbilitiesController;
use Jp\Dex\Application\Controllers\DexAbilityController;
use Jp\Dex\Application\Controllers\DexMovesController;
use Jp\Dex\Application\Controllers\DexMoveController;
use Jp\Dex\Application\Controllers\DexNaturesController;
use Jp\Dex\Application\Controllers\DexPokemonController;
use Jp\Dex\Application\Controllers\DexPokemonsController;
use Jp\Dex\Application\Controllers\DexTypeController;
use Jp\Dex\Application\Controllers\DexTypesController;
use Jp\Dex\Application\Controllers\ErrorController;
use Jp\Dex\Application\Controllers\IndexController;
use Jp\Dex\Application\Controllers\NotFoundController;
use Jp\Dex\Application\Controllers\StatsAbilityController;
use Jp\Dex\Application\Controllers\StatsAveragedLeadsController;
use Jp\Dex\Application\Controllers\StatsAveragedPokemonController;
use Jp\Dex\Application\Controllers\StatsAveragedUsageController;
use Jp\Dex\Application\Controllers\StatsIndexController;
use Jp\Dex\Application\Controllers\StatsItemController;
use Jp\Dex\Application\Controllers\StatsLeadsController;
use Jp\Dex\Application\Controllers\StatsMonthController;
use Jp\Dex\Application\Controllers\StatsMoveController;
use Jp\Dex\Application\Controllers\StatsPokemonController;
use Jp\Dex\Application\Controllers\StatsUsageController;
use Jp\Dex\Application\Controllers\TrendChartController;
use Jp\Dex\Application\Middleware\AjaxErrorMiddleware;
use Jp\Dex\Application\Middleware\CurrentStatsMiddleware;
use Jp\Dex\Application\Middleware\HtmlErrorMiddleware;
use Jp\Dex\Application\Middleware\JsonRequestMiddleware;
use Jp\Dex\Application\Middleware\LanguageMiddleware;
use Jp\Dex\Presentation\BreedingChainsView;
use Jp\Dex\Presentation\DexAbilitiesView;
use Jp\Dex\Presentation\DexAbilityView;
use Jp\Dex\Presentation\DexMovesView;
use Jp\Dex\Presentation\DexMoveView;
use Jp\Dex\Presentation\DexNaturesView;
use Jp\Dex\Presentation\DexPokemonView;
use Jp\Dex\Presentation\DexPokemonsView;
use Jp\Dex\Presentation\DexTypesView;
use Jp\Dex\Presentation\DexTypeView;
use Jp\Dex\Presentation\ErrorView;
use Jp\Dex\Presentation\IndexView;
use Jp\Dex\Presentation\NotFoundView;
use Jp\Dex\Presentation\StatsAbilityView;
use Jp\Dex\Presentation\StatsAveragedLeadsView;
use Jp\Dex\Presentation\StatsAveragedPokemonView;
use Jp\Dex\Presentation\StatsAveragedUsageView;
use Jp\Dex\Presentation\StatsIndexView;
use Jp\Dex\Presentation\StatsItemView;
use Jp\Dex\Presentation\StatsLeadsView;
use Jp\Dex\Presentation\StatsMonthView;
use Jp\Dex\Presentation\StatsMoveView;
use Jp\Dex\Presentation\StatsPokemonView;
use Jp\Dex\Presentation\StatsUsageView;
use Jp\Dex\Presentation\TrendChartView;

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
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/dex/$generationIdentifier/abilities", [
		'controllerClass' => DexAbilitiesController::class,
		'controllerMethod' => 'index',
		'viewClass' => DexAbilitiesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/dex/$generationIdentifier/abilities/$abilityIdentifier", [
		'controllerClass' => DexAbilityController::class,
		'controllerMethod' => 'index',
		'viewClass' => DexAbilityView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/dex/$generationIdentifier/moves", [
		'controllerClass' => DexMovesController::class,
		'controllerMethod' => 'index',
		'viewClass' => DexMovesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/dex/$generationIdentifier/moves/$moveIdentifier", [
		'controllerClass' => DexMoveController::class,
		'controllerMethod' => 'index',
		'viewClass' => DexMoveView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/dex/$generationIdentifier/natures", [
		'controllerClass' => DexNaturesController::class,
		'controllerMethod' => 'index',
		'viewClass' => DexNaturesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/dex/$generationIdentifier/pokemon", [
		'controllerClass' => DexPokemonsController::class,
		'controllerMethod' => 'index',
		'viewClass' => DexPokemonsView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/dex/$generationIdentifier/pokemon/$pokemonIdentifier", [
		'controllerClass' => DexPokemonController::class,
		'controllerMethod' => 'index',
		'viewClass' => DexPokemonView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/dex/$generationIdentifier/types", [
		'controllerClass' => DexTypesController::class,
		'controllerMethod' => 'index',
		'viewClass' => DexTypesView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/dex/$generationIdentifier/types/$typeIdentifier", [
		'controllerClass' => DexTypeController::class,
		'controllerMethod' => 'index',
		'viewClass' => DexTypeView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/dex/$generationIdentifier/pokemon/$pokemonIdentifier/breeding/$moveIdentifier/$versionGroupIdentifier", [
		'controllerClass' => BreedingChainsController::class,
		'controllerMethod' => 'index',
		'viewClass' => BreedingChainsView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', '/stats', [
		'controllerClass' => StatsIndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => StatsIndexView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$month", [
		'controllerClass' => StatsMonthController::class,
		'controllerMethod' => 'index',
		'viewClass' => StatsMonthView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating", [
		'controllerClass' => StatsUsageController::class,
		'controllerMethod' => 'setData',
		'viewClass' => StatsUsageView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/current", [
		'controllerClass' => StatsUsageController::class,
		'controllerMethod' => 'setData',
		'viewClass' => StatsUsageView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
			CurrentStatsMiddleware::class,
		],
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/leads", [
		'controllerClass' => StatsLeadsController::class,
		'controllerMethod' => 'setData',
		'viewClass' => StatsLeadsView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/pokemon/$pokemonIdentifier", [
		'controllerClass' => StatsPokemonController::class,
		'controllerMethod' => 'setData',
		'viewClass' => StatsPokemonView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/abilities/$abilityIdentifier", [
		'controllerClass' => StatsAbilityController::class,
		'controllerMethod' => 'setData',
		'viewClass' => StatsAbilityView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/items/$itemIdentifier", [
		'controllerClass' => StatsItemController::class,
		'controllerMethod' => 'setData',
		'viewClass' => StatsItemView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/moves/$moveIdentifier", [
		'controllerClass' => StatsMoveController::class,
		'controllerMethod' => 'setData',
		'viewClass' => StatsMoveView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	// Averaged
	['GET', "/stats/$start-to-$end/$formatIdentifier/$rating", [
		'controllerClass' => StatsAveragedUsageController::class,
		'controllerMethod' => 'setData',
		'viewClass' => StatsAveragedUsageView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$start-to-$end/$formatIdentifier/$rating/leads", [
		'controllerClass' => StatsAveragedLeadsController::class,
		'controllerMethod' => 'setData',
		'viewClass' => StatsAveragedLeadsView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$start-to-$end/$formatIdentifier/$rating/pokemon/$pokemonIdentifier", [
		'controllerClass' => StatsAveragedPokemonController::class,
		'controllerMethod' => 'setData',
		'viewClass' => StatsAveragedPokemonView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	// Charts
	['GET', '/stats/trends/chart', [
		'controllerClass' => TrendChartController::class,
		'controllerMethod' => 'index',
		'viewClass' => TrendChartView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['POST', '/stats/trends/chart', [
		'controllerClass' => TrendChartController::class,
		'controllerMethod' => 'ajax',
		'viewClass' => TrendChartView::class,
		'viewMethod' => 'ajax',
		'middlewareClasses' => [
			AjaxErrorMiddleware::class,
			LanguageMiddleware::class,
			JsonRequestMiddleware::class,
		],
	]],


	// Errors

	['GET', '/404', [
		'controllerClass' => NotFoundController::class,
		'controllerMethod' => 'get404',
		'viewClass' => NotFoundView::class,
		'viewMethod' => 'get404',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', '/405', [
		'controllerClass' => ErrorController::class,
		'controllerMethod' => 'getError',
		'viewClass' => ErrorView::class,
		'viewMethod' => 'getError',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', '/error', [
		'controllerClass' => ErrorController::class,
		'controllerMethod' => 'getError',
		'viewClass' => ErrorView::class,
		'viewMethod' => 'getError',
		'middlewareClasses' => [
			LanguageMiddleware::class,
		],
	]],
];

// Route dispatching.
$routeDispatcher = \FastRoute\simpleDispatcher(
	function (\FastRoute\RouteCollector $routeCollector) use ($routes) {
		foreach ($routes as $route) {
			$routeCollector->addRoute(...$route);
		}
	}
);

return $routeDispatcher;
