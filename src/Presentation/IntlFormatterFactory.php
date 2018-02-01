<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use IntlDateFormatter;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageRepositoryInterface;
use NumberFormatter;

class IntlFormatterFactory
{
	/** @var LanguageRepositoryInterface $languageRepository */
	private $languageRepository;

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
	 * Create the IntlFormatter for this language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return IntlFormatter
	 */
	public function createFor(LanguageId $languageId) : IntlFormatter
	{
		$language = $this->languageRepository->getById($languageId);

		$dateFormatter = new IntlDateFormatter(
			$language->getLocale(),
			IntlDateFormatter::LONG,
			IntlDateFormatter::NONE,
			null,
			null,
			$language->getDateFormat()
		);

		$numberFormatter = new NumberFormatter(
			$language->getLocale(),
			NumberFormatter::DECIMAL
		);
		$numberFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 5);

		$percentFormatter = new NumberFormatter(
			$language->getLocale(),
			NumberFormatter::PERCENT
		);
		$percentFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 5);

		return new IntlFormatter(
			$dateFormatter,
			$numberFormatter,
			$percentFormatter
		);
	}
}
