<?php
declare(strict_types=1);

// If we're not on production, our environment variables may not be set.
// In that case, we need to load them into the environment.
if (getenv('ENVIRONMENT') !== 'production') {
	$dotenv = new \Dotenv\Dotenv(__DIR__);
	$dotenv->load();
}
