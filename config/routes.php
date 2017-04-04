<?php
declare(strict_types=1);

$routes = [
	// API

	// one pokemon in all ratings.
	['GET', '/api/stats/usage/format/{format_identifier:[-\w]+}/pokemon/{pokemon_identifier:[-\w]+}', [
		'controllerClass' => UsageController::class,
		'controllerMethod' => 'getUsage',
		'viewClass' => UsageView::class,
		'viewMethod' => 'getUsage',
		'middlewareClasses' => [
		],
	]],

	// one pokemon in all ratings
	['GET', '/api/stats/leads/format/{format_identifier:[-\w]+}/pokemon/{pokemon_identifier:[-\w]+}', [
		'controllerClass' => TODO::class,
		'controllerMethod' => 'TODO',
		'viewClass' => TODO::class,
		'viewMethod' => 'TODO',
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
];

return $routes;
