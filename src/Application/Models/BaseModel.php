<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageName;
use Jp\Dex\Domain\Languages\LanguageNameRepositoryInterface;

final class BaseModel
{
	private LanguageId $currentLanguageId;


	/** @var LanguageName[] $languageNames */
	private array $languageNames = [];


	public function __construct(
		LanguageNameRepositoryInterface $languageNameRepository,
	) {
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
