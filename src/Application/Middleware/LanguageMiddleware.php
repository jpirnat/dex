<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

use DateTime;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageNotFoundException;
use Jp\Dex\Domain\Languages\LanguageRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LanguageMiddleware implements MiddlewareInterface
{
	/** @var LanguageRepositoryInterface $languageRepository */
	private $languageRepository;

	/** @var string $LANGUAGE_PARAMETER */
	private const LANGUAGE_PARAMETER = 'language';

	/** @var string $LANGUAGE_COOKIE */
	private const LANGUAGE_COOKIE = 'language';

	/** @var int $DEFAULT_LANGUAGE_ID */
	private const DEFAULT_LANGUAGE_ID = LanguageId::ENGLISH;

	/**
	 * Constructor.
	 *
	 * @param LanguageRepositoryInterface $languageRepository
	 */
	public function __construct(LanguageRepositoryInterface $languageRepository)
	{
		$this->languageRepository = $languageRepository;
	}

	/**
	 * Set a request attribute for the user's language.
	 * If the user is requesting to change their language, use that language.
	 * Or, if the request contains a language cookie, use that language.
	 * Or, use the default language.
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
		$languageId = self::DEFAULT_LANGUAGE_ID;

		// If the user is requesting to change their language, use that language.
		$setNewLanguage = isset($request->getQueryParams()[self::LANGUAGE_PARAMETER]);
		if ($setNewLanguage) {
			try {
				// Ensure that the requested language is valid.
				$language = $this->languageRepository->getById(
					new LanguageId(
						(int) $request->getQueryParams()[self::LANGUAGE_PARAMETER]
					)
				);
				$languageId = $language->getId()->value();
			} catch (LanguageNotFoundException $e) {
				// The user tried setting an invalid language.
				$setNewLanguage = false;
			}
		}

		// Or, If the request contains a language cookie, use that language.
		if (!$setNewLanguage && isset($request->getCookieParams()[self::LANGUAGE_COOKIE])) {
			// If no new language is being set, use the existing language cookie
			// or the default language.
			$languageId = $request->getCookieParams()[self::LANGUAGE_COOKIE];
		}

		$request = $request->withAttribute('languageId', $languageId);

		$response = $handler->handle($request);

		// If the user is requesting to change their language, store it in a cookie.
		if ($setNewLanguage) {
			/** @var SetCookie $setCookie */
			$setCookie = SetCookie::create(self::LANGUAGE_COOKIE);
			$setCookie = $setCookie->withValue($languageId);
			$setCookie = $setCookie->withExpires(new DateTime('+5 years'));
			$setCookie = $setCookie->withPath('/');
			$response = FigResponseCookies::set($response, $setCookie);
		}

		return $response;
	}
}
