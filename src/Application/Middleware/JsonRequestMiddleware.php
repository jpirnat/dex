<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class JsonRequestMiddleware implements MiddlewareInterface
{
	/**
	 * If the request body is JSON, parse it
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
		$contentType = $request->getHeader('Content-Type')[0] ?? '';

		if ($contentType === 'application/json') {
			$body = json_decode(file_get_contents('php://input'), true);

			$request = $request->withParsedBody($body);
		}

		return $handler->handle($request);
	}
}
