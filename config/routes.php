<?php
declare(strict_types=1);

use Jp\Dex\Application\Controllers\LeadsController;
use Jp\Dex\Application\Controllers\UsageController;
use Jp\Dex\Presentation\LeadsView;
use Jp\Dex\Presentation\UsageView;

// Route definitions.
$routes = [
	// API

	// one pokemon in all ratings.
	['GET', '/api/stats/usage/format/{format_identifier:[-\w]+}/pokemon/{pokemon_identifier:[-\w]+}', [
		'controllerClass' => UsageController::class,
		'controllerMethod' => 'setUsage',
		'viewClass' => UsageView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// one pokemon in all ratings
	['GET', '/api/stats/leads/format/{format_identifier:[-\w]+}/pokemon/{pokemon_identifier:[-\w]+}', [
		'controllerClass' => LeadsController::class,
		'controllerMethod' => 'setUsage',
		'viewClass' => LeadsView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// all abilities in one rating
	['GET', '/api/stats/abilities/format/{format_identifier:[-\w]+}/rating/{rating:\d+}/pokemon/{pokemon_identifier:[-\w]+}', [
		'controllerClass' => TODO::class,
		'controllerMethod' => 'TODO',
		'viewClass' => TODO::class,
		'viewMethod' => 'TODO',
		'middlewareClasses' => [
		],
	]],

	// one ability in all ratings
	['GET', '/api/stats/abilities/format/{format_identifier:[-\w]+}/pokemon/{pokemon_identifier:[-\w]+}/ability/{ability_identifier:[-\w]+}', [
		'controllerClass' => TODO::class,
		'controllerMethod' => 'TODO',
		'viewClass' => TODO::class,
		'viewMethod' => 'TODO',
		'middlewareClasses' => [
		],
	]],

	// all items in one rating
	['GET', '/api/stats/items/format/{format_identifier:[-\w]+}/rating/{rating:\d+}/pokemon/{pokemon_identifier:[-\w]+}', [
		'controllerClass' => TODO::class,
		'controllerMethod' => 'TODO',
		'viewClass' => TODO::class,
		'viewMethod' => 'TODO',
		'middlewareClasses' => [
		],
	]],

	// one item in all ratings
	['GET', '/api/stats/items/format/{format_identifier:[-\w]+}/pokemon/{pokemon_identifier:[-\w]+}/item/{item_identifier:[-\w]+}', [
		'controllerClass' => TODO::class,
		'controllerMethod' => 'TODO',
		'viewClass' => TODO::class,
		'viewMethod' => 'TODO',
		'middlewareClasses' => [
		],
	]],

	// all moves in one rating
	['GET', '/api/stats/moves/format/{format_identifier:[-\w]+}/rating/{rating:\d+}/pokemon/{pokemon_identifier:[-\w]+}', [
		'controllerClass' => TODO::class,
		'controllerMethod' => 'TODO',
		'viewClass' => TODO::class,
		'viewMethod' => 'TODO',
		'middlewareClasses' => [
		],
	]],

	// one move in all ratings
	['GET', '/api/move/items/format/{format_identifier:[-\w]+}/pokemon/{pokemon_identifier:[-\w]+}/move/{move_identifier:[-\w]+}', [
		'controllerClass' => TODO::class,
		'controllerMethod' => 'TODO',
		'viewClass' => TODO::class,
		'viewMethod' => 'TODO',
		'middlewareClasses' => [
		],
	]],


	['GET', '/404', [
		'controllerClass' => TODO::class,
		'controllerMethod' => 'TODO',
		'viewClass' => TODO::class,
		'viewMethod' => 'TODO',
		'middlewareClasses' => [
		],
	]],

	['GET', '/405', [
		'controllerClass' => TODO::class,
		'controllerMethod' => 'TODO',
		'viewClass' => TODO::class,
		'viewMethod' => 'TODO',
		'middlewareClasses' => [
		],
	]],

	['GET', '/error', [
		'controllerClass' => TODO::class,
		'controllerMethod' => 'TODO',
		'viewClass' => TODO::class,
		'viewMethod' => 'TODO',
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
