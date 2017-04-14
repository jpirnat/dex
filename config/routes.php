<?php
declare(strict_types=1);

use Jp\Dex\Application\Controllers\AbilitiesController;
use Jp\Dex\Application\Controllers\ItemsController;
use Jp\Dex\Application\Controllers\LeadsController;
use Jp\Dex\Application\Controllers\MovesController;
use Jp\Dex\Application\Controllers\NotFoundController;
use Jp\Dex\Application\Controllers\UsageController;
use Jp\Dex\Presentation\AbilitiesView;
use Jp\Dex\Presentation\ItemsView;
use Jp\Dex\Presentation\LeadsView;
use Jp\Dex\Presentation\MovesView;
use Jp\Dex\Presentation\NotFoundView;
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
		'controllerClass' => AbilitiesController::class,
		'controllerMethod' => 'setRatingUsage',
		'viewClass' => AbilitiesView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// one ability in all ratings
	['GET', '/api/stats/abilities/format/{format_identifier:[-\w]+}/pokemon/{pokemon_identifier:[-\w]+}/ability/{ability_identifier:[-\w]+}', [
		'controllerClass' => AbilitiesController::class,
		'controllerMethod' => 'setAbilityUsage',
		'viewClass' => AbilitiesView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// all items in one rating
	['GET', '/api/stats/items/format/{format_identifier:[-\w]+}/rating/{rating:\d+}/pokemon/{pokemon_identifier:[-\w]+}', [
		'controllerClass' => ItemsController::class,
		'controllerMethod' => 'setRatingUsage',
		'viewClass' => ItemsView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// one item in all ratings
	['GET', '/api/stats/items/format/{format_identifier:[-\w]+}/pokemon/{pokemon_identifier:[-\w]+}/item/{item_identifier:[-\w]+}', [
		'controllerClass' => ItemsController::class,
		'controllerMethod' => 'setItemUsage',
		'viewClass' => ItemsView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// all moves in one rating
	['GET', '/api/stats/moves/format/{format_identifier:[-\w]+}/rating/{rating:\d+}/pokemon/{pokemon_identifier:[-\w]+}', [
		'controllerClass' => MovesController::class,
		'controllerMethod' => 'setRatingUsage',
		'viewClass' => MovesView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// one move in all ratings
	['GET', '/api/stats/moves/format/{format_identifier:[-\w]+}/pokemon/{pokemon_identifier:[-\w]+}/move/{move_identifier:[-\w]+}', [
		'controllerClass' => MovesController::class,
		'controllerMethod' => 'setMoveUsage',
		'viewClass' => MovesView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],


	['GET', '/404', [
		'controllerClass' => NotFoundController::class,
		'controllerMethod' => 'get404',
		'viewClass' => NotFoundView::class,
		'viewMethod' => 'get404',
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
