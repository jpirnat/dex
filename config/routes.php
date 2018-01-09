<?php
declare(strict_types=1);

use Jp\Dex\Application\Controllers\AbilitiesController;
use Jp\Dex\Application\Controllers\ErrorController;
use Jp\Dex\Application\Controllers\ItemsController;
use Jp\Dex\Application\Controllers\LanguageController;
use Jp\Dex\Application\Controllers\LeadsController;
use Jp\Dex\Application\Controllers\LeadsMonthController;
use Jp\Dex\Application\Controllers\MovesController;
use Jp\Dex\Application\Controllers\MovesetPokemonMonthController;
use Jp\Dex\Application\Controllers\MoveUsageMonthController;
use Jp\Dex\Application\Controllers\NotFoundController;
use Jp\Dex\Application\Controllers\UsageController;
use Jp\Dex\Application\Controllers\UsageMonthController;
use Jp\Dex\Application\Middleware\HtmlErrorMiddleware;
use Jp\Dex\Application\Middleware\LanguageMiddleware;
use Jp\Dex\Presentation\AbilitiesView;
use Jp\Dex\Presentation\ErrorView;
use Jp\Dex\Presentation\ItemsView;
use Jp\Dex\Presentation\LanguageView;
use Jp\Dex\Presentation\LeadsMonthView;
use Jp\Dex\Presentation\LeadsView;
use Jp\Dex\Presentation\MovesetPokemonMonthView;
use Jp\Dex\Presentation\MovesView;
use Jp\Dex\Presentation\MoveUsageMonthView;
use Jp\Dex\Presentation\NotFoundView;
use Jp\Dex\Presentation\UsageMonthView;
use Jp\Dex\Presentation\UsageView;

// Route definitions.
$routes = [
	// API

	// one pokemon in all ratings.
	['GET', '/api/stats/usage/format/{formatIdentifier:[-\w]+}/pokemon/{pokemonIdentifier:[-\w]+}', [
		'controllerClass' => UsageController::class,
		'controllerMethod' => 'setUsage',
		'viewClass' => UsageView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// one pokemon in all ratings
	['GET', '/api/stats/leads/format/{formatIdentifier:[-\w]+}/pokemon/{pokemonIdentifier:[-\w]+}', [
		'controllerClass' => LeadsController::class,
		'controllerMethod' => 'setUsage',
		'viewClass' => LeadsView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// all abilities in one rating
	['GET', '/api/stats/abilities/format/{formatIdentifier:[-\w]+}/rating/{rating:\d+}/pokemon/{pokemonIdentifier:[-\w]+}', [
		'controllerClass' => AbilitiesController::class,
		'controllerMethod' => 'setRatingUsage',
		'viewClass' => AbilitiesView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// one ability in all ratings
	['GET', '/api/stats/abilities/format/{formatIdentifier:[-\w]+}/pokemon/{pokemonIdentifier:[-\w]+}/ability/{ability_identifier:[-\w]+}', [
		'controllerClass' => AbilitiesController::class,
		'controllerMethod' => 'setAbilityUsage',
		'viewClass' => AbilitiesView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// all items in one rating
	['GET', '/api/stats/items/format/{formatIdentifier:[-\w]+}/rating/{rating:\d+}/pokemon/{pokemonIdentifier:[-\w]+}', [
		'controllerClass' => ItemsController::class,
		'controllerMethod' => 'setRatingUsage',
		'viewClass' => ItemsView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// one item in all ratings
	['GET', '/api/stats/items/format/{formatIdentifier:[-\w]+}/pokemon/{pokemonIdentifier:[-\w]+}/item/{item_identifier:[-\w]+}', [
		'controllerClass' => ItemsController::class,
		'controllerMethod' => 'setItemUsage',
		'viewClass' => ItemsView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// all moves in one rating
	['GET', '/api/stats/moves/format/{formatIdentifier:[-\w]+}/rating/{rating:\d+}/pokemon/{pokemonIdentifier:[-\w]+}', [
		'controllerClass' => MovesController::class,
		'controllerMethod' => 'setRatingUsage',
		'viewClass' => MovesView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// one move in all ratings
	['GET', '/api/stats/moves/format/{formatIdentifier:[-\w]+}/pokemon/{pokemonIdentifier:[-\w]+}/move/{move_identifier:[-\w]+}', [
		'controllerClass' => MovesController::class,
		'controllerMethod' => 'setMoveUsage',
		'viewClass' => MovesView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],



	['GET', '/', [
		'controllerClass' => IndexController::class,
		'controllerMethod' => 'index',
		'viewClass' => IndexView::class,
		'viewMethod' => 'index',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
		],
	]],

	[
		'GET',
		'/stats/usage/{year:\d+}/{month:\d+}/{formatIdentifier:[-\w]+}/{rating:\d+}',
		[
			'controllerClass' => UsageMonthController::class,
			'controllerMethod' => 'setData',
			'viewClass' => UsageMonthView::class,
			'viewMethod' => 'getData',
			'middlewareClasses' => [
				HtmlErrorMiddleware::class,
				LanguageMiddleware::class,
			],
		]
	],

	[
		'GET',
		'/stats/leads/{year:\d+}/{month:\d+}/{formatIdentifier:[-\w]+}/{rating:\d+}',
		[
			'controllerClass' => LeadsMonthController::class,
			'controllerMethod' => 'setData',
			'viewClass' => LeadsMonthView::class,
			'viewMethod' => 'getData',
			'middlewareClasses' => [
				HtmlErrorMiddleware::class,
				LanguageMiddleware::class,
			],
		]
	],

	[
		'GET',
		'/stats/moveset/{year:\d+}/{month:\d+}/{formatIdentifier:[-\w]+}/{rating:\d+}/{pokemonIdentifier:[-\w]+}',
		[
			'controllerClass' => MovesetPokemonMonthController::class,
			'controllerMethod' => 'setData',
			'viewClass' => MovesetPokemonMonthView::class,
			'viewMethod' => 'getData',
			'middlewareClasses' => [
				HtmlErrorMiddleware::class,
				LanguageMiddleware::class,
			],
		]
	],

	[
		'GET',
		'/stats/move-usage/{year:\d+}/{month:\d+}/{formatIdentifier:[-\w]+}/{rating:\d+}/{moveIdentifier:[-\w]+}',
		[
			'controllerClass' => MoveUsageMonthController::class,
			'controllerMethod' => 'setData',
			'viewClass' => MoveUsageMonthView::class,
			'viewMethod' => 'getData',
			'middlewareClasses' => [
				HtmlErrorMiddleware::class,
				LanguageMiddleware::class,
			],
		]
	],



	[
		'GET',
		'/language',
		[
			'controllerClass' => LanguageController::class,
			'controllerMethod' => 'setLanguage',
			'viewClass' => LanguageView::class,
			'viewMethod' => 'setLanguage',
			'middlewareClasses' => [
			],
		]
	],

	// Errors

	['GET', '/404', [
		'controllerClass' => NotFoundController::class,
		'controllerMethod' => 'get404',
		'viewClass' => NotFoundView::class,
		'viewMethod' => 'get404',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
		],
	]],

	['GET', '/405', [
		'controllerClass' => ErrorController::class,
		'controllerMethod' => 'getError',
		'viewClass' => ErrorView::class,
		'viewMethod' => 'getError',
		'middlewareClasses' => [
			HtmlErrorMiddleware::class,
		],
	]],

	['GET', '/error', [
		'controllerClass' => ErrorController::class,
		'controllerMethod' => 'getError',
		'viewClass' => ErrorView::class,
		'viewMethod' => 'getError',
		'middlewareClasses' => [
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
