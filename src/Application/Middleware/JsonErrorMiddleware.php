<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Run;

final readonly class JsonErrorMiddleware implements MiddlewareInterface
{
	public function __construct(
		private string $environment,
	) {}

	/**
	 * Intercept all errors and exceptions in the code and return an error json
	 * response object.
	 */
	public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler,
	) : ResponseInterface {
		if ($this->environment !== 'production') {
			// In development environments, we want to see the errors. They can
			// run, but they can't hide!
			$whoops = new Run();
			$whoops->prependHandler(new JsonResponseHandler());
			$whoops->register();

			return $handler->handle($request);
		}

		// In production environments, the user should not see PHP errors.
		// Instead, redirect them to our error page.
		try {
			return $handler->handle($request);
		} catch (Throwable) {
			return new JsonResponse(['error' => true]);
		}
	}
}
