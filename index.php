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


// Load dependency injection container.
$container = require __DIR__ . '/config/dependencies.php';
