<?php
declare(strict_types=1);

use Jp\Dex\Application\Controllers\AbilityUsageMonthController;
use Jp\Dex\Application\Controllers\BreedingChainsController;
use Jp\Dex\Application\Controllers\DexAbilitiesController;
use Jp\Dex\Application\Controllers\DexAbilityController;
use Jp\Dex\Application\Controllers\DexMoveController;
use Jp\Dex\Application\Controllers\DexNaturesController;
use Jp\Dex\Application\Controllers\DexPokemonController;
use Jp\Dex\Application\Controllers\DexTypeController;
use Jp\Dex\Application\Controllers\DexTypesController;
use Jp\Dex\Application\Controllers\ErrorController;
use Jp\Dex\Application\Controllers\IndexController;
use Jp\Dex\Application\Controllers\ItemUsageMonthController;
use Jp\Dex\Application\Controllers\LeadsAveragedController;
use Jp\Dex\Application\Controllers\LeadsMonthController;
use Jp\Dex\Application\Controllers\StatsMonthController;
use Jp\Dex\Application\Controllers\MovesetPokemonAveragedController;
use Jp\Dex\Application\Controllers\MovesetPokemonMonthController;
use Jp\Dex\Application\Controllers\MoveUsageMonthController;
use Jp\Dex\Application\Controllers\NotFoundController;
use Jp\Dex\Application\Controllers\StatsIndexController;
use Jp\Dex\Application\Controllers\TrendChartController;
use Jp\Dex\Application\Controllers\UsageAveragedController;
use Jp\Dex\Application\Controllers\UsageMonthController;
use Jp\Dex\Application\Middleware\AjaxErrorMiddleware;
use Jp\Dex\Application\Middleware\HtmlErrorMiddleware;
use Jp\Dex\Application\Middleware\JsonRequestMiddleware;
use Jp\Dex\Application\Middleware\LanguageMiddleware;
use Jp\Dex\Presentation\AbilityUsageMonthView;
use Jp\Dex\Presentation\BreedingChainsView;
use Jp\Dex\Presentation\DexAbilitiesView;
use Jp\Dex\Presentation\DexAbilityView;
use Jp\Dex\Presentation\DexMoveView;
use Jp\Dex\Presentation\DexNaturesView;
use Jp\Dex\Presentation\DexPokemonView;
use Jp\Dex\Presentation\DexTypesView;
use Jp\Dex\Presentation\DexTypeView;
use Jp\Dex\Presentation\ErrorView;
use Jp\Dex\Presentation\IndexView;
use Jp\Dex\Presentation\ItemUsageMonthView;
use Jp\Dex\Presentation\LeadsAveragedView;
use Jp\Dex\Presentation\LeadsMonthView;
use Jp\Dex\Presentation\StatsMonthView;
use Jp\Dex\Presentation\MovesetPokemonAveragedView;
use Jp\Dex\Presentation\MovesetPokemonMonthView;
use Jp\Dex\Presentation\MoveUsageMonthView;
use Jp\Dex\Presentation\NotFoundView;
use Jp\Dex\Presentation\StatsIndexView;
use Jp\Dex\Presentation\TrendChartView;
use Jp\Dex\Presentation\UsageAveragedView;
use Jp\Dex\Presentation\UsageMonthView;

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
		'controllerClass' => UsageMonthController::class,
		'controllerMethod' => 'setData',
		'viewClass' => UsageMonthView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/leads", [
		'controllerClass' => LeadsMonthController::class,
		'controllerMethod' => 'setData',
		'viewClass' => LeadsMonthView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/pokemon/$pokemonIdentifier", [
		'controllerClass' => MovesetPokemonMonthController::class,
		'controllerMethod' => 'setData',
		'viewClass' => MovesetPokemonMonthView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/abilities/$abilityIdentifier", [
		'controllerClass' => AbilityUsageMonthController::class,
		'controllerMethod' => 'setData',
		'viewClass' => AbilityUsageMonthView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/items/$itemIdentifier", [
		'controllerClass' => ItemUsageMonthController::class,
		'controllerMethod' => 'setData',
		'viewClass' => ItemUsageMonthView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$month/$formatIdentifier/$rating/moves/$moveIdentifier", [
		'controllerClass' => MoveUsageMonthController::class,
		'controllerMethod' => 'setData',
		'viewClass' => MoveUsageMonthView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	// Averaged
	['GET', "/stats/$start-to-$end/$formatIdentifier/$rating", [
		'controllerClass' => UsageAveragedController::class,
		'controllerMethod' => 'setData',
		'viewClass' => UsageAveragedView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$start-to-$end/$formatIdentifier/$rating/leads", [
		'controllerClass' => LeadsAveragedController::class,
		'controllerMethod' => 'setData',
		'viewClass' => LeadsAveragedView::class,
		'viewMethod' => 'getData',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
			LanguageMiddleware::class,
		],
	]],

	['GET', "/stats/$start-to-$end/$formatIdentifier/$rating/pokemon/$pokemonIdentifier", [
		'controllerClass' => MovesetPokemonAveragedController::class,
		'controllerMethod' => 'setData',
		'viewClass' => MovesetPokemonAveragedView::class,
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
