<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

use DateTime;
use Jp\Dex\Application\CookieNames;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\Usage\UsageQueriesInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class CurrentStatsMiddleware implements MiddlewareInterface
{
	private const int DEFAULT_FORMAT_ID = FormatId::GEN_9_OU;
	private const int DEFAULT_RATING = 1695;

	public function __construct(
		private FormatRepositoryInterface $formatRepository,
		private UsageQueriesInterface $usageQueries,
	) {}

	/**
	 * Set request attributes for the stats usage page so it shows the latest
	 * data for the user's default format.
	 */
	public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler,
	) : ResponseInterface {
		// Get the format and rating from the user's cookies.
		$cookies = $request->getCookieParams();
		$formatIdentifier = $cookies[CookieNames::FORMAT] ?? '';
		$rating = $cookies[CookieNames::RATING] ?? '';

		if ($formatIdentifier) {
			$format = $this->formatRepository->getByIdentifier(
				$formatIdentifier,
				new LanguageId(LanguageId::ENGLISH), // The language doesn't matter.
			);
		} else {
			// If the user doesn't have a format cookie, use default format.
			$formatId = new FormatId(self::DEFAULT_FORMAT_ID);
			$format = $this->formatRepository->getById(
				$formatId,
				new LanguageId(LanguageId::ENGLISH), // The language doesn't matter.
			);
		}

		// If the user doesn't have a rating cookie, use default rating.
		if ($rating === '') {
			$rating = (string) self::DEFAULT_RATING;
		}

		// Get the latest month of data for the format.
		$month = $this->usageQueries->getNewest($format->id);
		if ($month === null) {
			// This format has no data ever, so it doesn't matter what month we use.
			$month = new DateTime('-1 month');
		}

		// Set the attributes.
		$request = $request->withAttribute('month', $month->format('Y-m'));
		$request = $request->withAttribute('formatIdentifier', $format->identifier);
		$request = $request->withAttribute('rating', $rating);

		return $handler->handle($request);
	}
}
