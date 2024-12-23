<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use IntlDateFormatter;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageRepositoryInterface;
use NumberFormatter;

/**
 * Many view classes have a dependency on IntlFormatter. However, IntlFormatter
 * has a run-time dependency on LanguageId. So, those view classes use this
 * factory class as their injected dependency instead.
 */
final readonly class IntlFormatterFactory
{
	public function __construct(
		private LanguageRepositoryInterface $languageRepository,
	) {}

	/**
	 * Create the IntlFormatter for this language.
	 */
	public function createFor(LanguageId $languageId) : IntlFormatter
	{
		$language = $this->languageRepository->getById($languageId);

		$dateFormatter = new IntlDateFormatter(
			$language->locale,
			IntlDateFormatter::LONG,
			IntlDateFormatter::NONE,
			null,
			null,
			$language->dateFormat,
		);

		$numberFormatter = new NumberFormatter(
			$language->locale,
			NumberFormatter::DECIMAL,
		);
		$numberFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 5);

		$percentFormatter = new NumberFormatter(
			$language->locale,
			NumberFormatter::PERCENT,
		);
		$percentFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 5);

		$changeFormatter = new NumberFormatter(
			$language->locale,
			NumberFormatter::PERCENT,
		);
		$changeFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 5);
		$changeFormatter->setTextAttribute(NumberFormatter::POSITIVE_PREFIX, '+');

		return new IntlFormatter(
			$dateFormatter,
			$numberFormatter,
			$percentFormatter,
			$changeFormatter,
		);
	}
}
