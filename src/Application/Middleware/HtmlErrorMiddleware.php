<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Zend\Diactoros\Response\RedirectResponse;

class HtmlErrorMiddleware implements MiddlewareInterface
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
	 * @param DelegateInterface $delegate
	 *
	 * @return ResponseInterface
	 */
	public function process(
		ServerRequestInterface $request,
		DelegateInterface $delegate
	) : ResponseInterface {
		if ($this->environment === 'production') {
			// Intercept all errors on production.
			try {
				$response = $delegate->process($request);
			} catch (Throwable $e) {
				// TODO: Log the error.
				$response = new RedirectResponse('/error');
			}
		} else {
			// Disregard errors in development environment.
			$response = $delegate->process($request);
		}

		return $response;
	}
}
