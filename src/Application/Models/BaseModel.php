<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageName;
use Jp\Dex\Domain\Languages\LanguageNameRepositoryInterface;

final class BaseModel
{
	private int $currentYear;
	private LanguageId $currentLanguageId;


	/** @var LanguageName[] $languageNames */
	private array $languageNames = [];


	/**
	 * Constructor.
	 *
	 * @param LanguageNameRepositoryInterface $languageNameRepository
	 */
	public function __construct(
		LanguageNameRepositoryInterface $languageNameRepository
	) {
		$today = new DateTime('today');
		$this->currentYear = (int) $today->format('Y');

		$this->languageNames = $languageNameRepository->getInOwnLanguages();
	}

	/**
	 * Set the current language id.
	 *
	 * @param LanguageId $currentLanguageId
	 *
	 * @return void
	 */
	public function setCurrentLanguageId(LanguageId $currentLanguageId) : void
	{
		$this->currentLanguageId = $currentLanguageId;
	}

	/**
	 * Get the current year, for the copyright in the footer.
	 *
	 * @return int
	 */
	public function getCurrentYear() : int
	{
		return $this->currentYear;
	}

	/**
	 * Get the current language id.
	 *
	 * @return LanguageId
	 */
	public function getCurrentLanguageId() : LanguageId
	{
		return $this->currentLanguageId;
	}

	/**
	 * Get the language names, for the language select in the footer.
	 *
	 * @return LanguageName[]
	 */
	public function getLanguageNames() : array
	{
		return $this->languageNames;
	}
}
