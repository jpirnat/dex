<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Zend\Diactoros\Response\RedirectResponse;

final class HtmlErrorMiddleware implements MiddlewareInterface
{
	/** @var string $environment */
	private $environment;

	/**
	 * Constructor.
	 *
	 * @param string $environment
	 */
	public function __construct(string $environment)
	{
		$this->environment = $environment;
	}

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
		if ($this->environment === 'production') {
			// In production environments, the user should not see PHP errors.
			// Instead, redirect them to our error page.
			try {
				$response = $handler->handle($request);
			} catch (Throwable $e) {
				$response = new RedirectResponse('/error');
			}
		} else {
			// In development environments, we want to see the errors. They can
			// run, but they can't hide!
			$whoops = new Run();
			$whoops->pushHandler(new PrettyPageHandler());
			$whoops->register();

			$response = $handler->handle($request);
		}

		return $response;
	}
}
