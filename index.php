<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/environment.php';
$container = require __DIR__ . '/config/container.php';


// Tell PHP that we're using UTF-8 strings until the end of the script.
mb_internal_encoding('UTF-8');

// Tell PHP that we'll be outputting UTF-8 to the browser.
mb_http_output('UTF-8');


// Security headers.
// TODO: Content-Security-Policy
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
// TODO: Move security headers to a middleware.


// Create the request.
$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals(
	$_SERVER,
	$_GET,
	$_POST,
	$_COOKIE,
	$_FILES
);


// Load route dispatcher.
$routeDispatcher = \FastRoute\cachedDispatcher(
	function (\FastRoute\RouteCollector $routeCollector) {
		$routes = require __DIR__ . '/config/routes.php';
		foreach ($routes as $route) {
			$routeCollector->addRoute(...$route);
		}
	},
	[
		'cacheFile' => __DIR__ . '/config/cache/routes.php',
	],
);


// Dispatch the route.
$routeInfo = $routeDispatcher->dispatch(
	$request->getMethod(),
	$requestUri = $request->getUri()->getPath()
);

if ($routeInfo[0] === \FastRoute\Dispatcher::FOUND) {
	// Get the route's controller, view, and middleware configuration.
	$controllerClass = $routeInfo[1]['controllerClass'];
	$controllerMethod = $routeInfo[1]['controllerMethod'];

	$viewClass = $routeInfo[1]['viewClass'];
	$viewMethod = $routeInfo[1]['viewMethod'];

	$middlewareClasses = $routeInfo[1]['middlewareClasses'] ?? [];

	// Add route parameters to the request as attributes.
	foreach ($routeInfo[2] as $key => $value) {
		$request = $request->withAttribute($key, $value);
	}

	// Instantiate the controller and the view.
	$controller = $container->get($controllerClass);
	$view = $container->get($viewClass);

	// Wrap the application execution in a closure for later.
	$app = function (\Psr\Http\Message\ServerRequestInterface $request) use (
		$controller,
		$controllerMethod,
		$view,
		$viewMethod
	) : \Psr\Http\Message\ResponseInterface {
		$controller->$controllerMethod($request);
		return $view->$viewMethod();
	};

	// Execute the route's application and middleware stack.
	$middlewareDispatcher = new \Jp\Middleware\Dispatcher($container, $app);
	$middlewareDispatcher->addMiddlewares($middlewareClasses);
	$response = $middlewareDispatcher->handle($request);
} else {
	$response = new \Laminas\Diactoros\Response\RedirectResponse('/404');
}

// Emit the response.
$emitter = new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter();
$emitter->emit($response);
