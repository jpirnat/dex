<?php
declare(strict_types=1);


// We use Composer for all our autoloading.
require __DIR__ . '/vendor/autoload.php';


// If we're not on production, our environment variables may not be set.
// In that case, we need to load them into the environment.
if (getenv('ENVIRONMENT') !== 'production') {
	$dotenv = new \Dotenv\Dotenv(__DIR__);
	$dotenv->load();
}


// If we're not on production, we're probably writing code or testing it.
// So, let's give ourselves a prettier debugging suite.
if (getenv('ENVIRONMENT') !== 'production') {
	$whoops = new \Whoops\Run;
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
	$whoops->register();
}


// Tell PHP that we're using UTF-8 strings until the end of the script.
mb_internal_encoding('UTF-8');

// Tell PHP that we'll be outputting UTF-8 to the browser.
mb_http_output('UTF-8');


// Security headers.
// TODO: Content-Security-Policy
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
// TODO: Move security headers to a middleware.


// Create the request.
$request = \Zend\Diactoros\ServerRequestFactory::fromGlobals(
	$_SERVER,
	$_GET,
	$_POST,
	$_COOKIE,
	$_FILES
);


// Load dependency injection container.
$container = require __DIR__ . '/config/dependencies.php';


// Load route dispatcher.
$routeDispatcher = require __DIR__ . '/config/routes.php';


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
	$response = $middlewareDispatcher->process($request);
} elseif ($routeInfo[0] === \FastRoute\Dispatcher::NOT_FOUND) {
	$response = new \Zend\Diactoros\Response\RedirectResponse('/404');
} elseif ($routeInfo[0] === \FastRoute\Dispatcher::METHOD_NOT_ALLOWED) {
	$allowedMethods = $routeInfo[1];
	$response = new \Zend\Diactoros\Response\RedirectResponse('/405');
} else {
	// This should never happen.
	$response = new \Zend\Diactoros\Response\RedirectResponse('/');
}

// Emit the response.
$emitter = new \Zend\Diactoros\Response\SapiEmitter();
$emitter->emit($response);
