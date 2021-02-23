<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

final class HtmlErrorMiddleware implements MiddlewareInterface
{
	public function __construct(
		private string $environment,
	) {}

	/**
	 * Intercept all errors and exceptions in the code and redirect the user to
	 * the Error page.
	 *
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 */
	public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler
	) : ResponseInterface {
		if ($this->environment !== 'production') {
			// In development environments, we want to see the errors. They can
			// run, but they can't hide!
			$whoops = new Run();
			$whoops->prependHandler(new PrettyPageHandler());
			$whoops->register();

			return $handler->handle($request);
		}

		// In production environments, the user should not see PHP errors.
		// Instead, redirect them to our error page.
		try {
			return $handler->handle($request);
		} catch (Throwable) {
			return new RedirectResponse('/error');
		}
	}
}
