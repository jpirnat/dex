<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

final readonly class MiddlewareGroups
{
	/** @var string[] $NONE */
	public const NONE = [];

	/**
	 * The default middleware group for index routes.
	 *
	 * @var string[] $HTML
	 */
	public const HTML = [
		HtmlErrorMiddleware::class,
		LanguageMiddleware::class,
	];

	/**
	 * The default middleware group for json routes.
	 *
	 * @var string[] $JSON
	 */
	public const JSON = [
		JsonErrorMiddleware::class,
		LanguageMiddleware::class,
	];

	/**
	 * Used by /stats/current to add route attributes for month, formatId, and rating.
	 *
	 * @var string[] $CURRENT_STATS
	 */
	public const CURRENT_STATS = [
		JsonErrorMiddleware::class,
		LanguageMiddleware::class,
		CurrentStatsMiddleware::class,
	];

	/**
	 * The /error page should use this group instead of self::HTML, so there
	 * won't be any risk of infinite redirect loop back to the /error page.
	 *
	 * @var string[] $ERROR
	 */
	public const ERROR = [
		LanguageMiddleware::class,
	];
}
