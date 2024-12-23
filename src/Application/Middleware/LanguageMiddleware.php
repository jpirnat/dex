<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

use DateTime;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Jp\Dex\Application\CookieNames;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageNotFoundException;
use Jp\Dex\Domain\Languages\LanguageRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class LanguageMiddleware implements MiddlewareInterface
{
	private const string LANGUAGE_PARAMETER = 'language';

	private const int DEFAULT_LANGUAGE_ID = LanguageId::ENGLISH;

	public function __construct(
		private LanguageRepositoryInterface $languageRepository,
	) {}

	/**
	 * Set a request attribute for the user's language.
	 * If the user is requesting to change their language, use that language.
	 * Or, if the request contains a language cookie, use that language.
	 * Or, use the default language.
	 */
	public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler,
	) : ResponseInterface {
		// If the user is requesting to change their language, use that language.
		$setNewLanguage = false;
		$queryParameter = $request->getQueryParams()[self::LANGUAGE_PARAMETER] ?? '';
		if ($queryParameter) {
			$setNewLanguage = true;
			$languageId = $queryParameter;
		}

		// Or, if the request contains a language cookie, use that language.
		$cookieValue = $request->getCookieParams()[CookieNames::LANGUAGE] ?? '';
		if (!$setNewLanguage && $cookieValue) {
			$languageId = $cookieValue;
		}

		if (isset($languageId)) {
			// Ensure that the requested language is valid.
			try {
				$languageId = new LanguageId((int) $languageId);
				$language = $this->languageRepository->getById($languageId);
				$languageId = $language->id->value();
			} catch (LanguageNotFoundException) {
				unset($languageId);
				$setNewLanguage = false;
			}
		}

		// Or, use the default language.
		if (!isset($languageId)) {
			$languageId = self::DEFAULT_LANGUAGE_ID;
		}

		$request = $request->withAttribute('languageId', $languageId);
		$response = $handler->handle($request);

		// If the user is requesting to change their language, store it in a cookie.
		if ($setNewLanguage) {
			$setCookie = SetCookie::create(CookieNames::LANGUAGE);
			$setCookie = $setCookie->withValue((string) $languageId);
			$setCookie = $setCookie->withExpires(new DateTime('+5 years'));
			$setCookie = $setCookie->withPath('/');
			$response = FigResponseCookies::set($response, $setCookie);
		}

		return $response;
	}
}
