<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LanguageMiddleware implements MiddlewareInterface
{
	/** @var int $DEFAULT_LANGUAGE */
	public const DEFAULT_LANGUAGE = 2; // English

	/**
	 * Set a request attribute for the user's language, either from the user's
	 * language cookie (if it exists), or the default language.
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
		$languageId = $request->getCookieParams()['languageId'] ?? self::DEFAULT_LANGUAGE;

		$request = $request->withAttribute('languageId', $languageId);

		return $handler->handle($request);
	}
}
