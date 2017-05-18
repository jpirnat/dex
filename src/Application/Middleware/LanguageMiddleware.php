<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LanguageMiddleware implements MiddlewareInterface
{
	/** @var int $DEFAULT_LANGUAGE */
	public const DEFAULT_LANGUAGE = 2; // English

	/**
	 * Set a request attribute for the user's language, either from the user's
	 * language cookie (if it exists), or the default language.
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
		$language = $request->getCookieParams()['language'] ?? self::DEFAULT_LANGUAGE;

		$request = $request->withAttribute('language', $language);

		return $delegate->process($request);
	}
}
