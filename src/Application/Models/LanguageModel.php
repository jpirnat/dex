<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageNotFoundException;
use Jp\Dex\Domain\Languages\LanguageRepositoryInterface;

class LanguageModel
{
	/** @var LanguageRepositoryInterface $languageRepository */
	private $languageRepository;

	/** @var LanguageId|null $languageId */
	private $languageId;

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
	 * Set the user's language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setLanguage(LanguageId $languageId) : void
	{
		try {
			// Do this so the user can't set an invalid language.
			$language = $this->languageRepository->getById($languageId);
		} catch (LanguageNotFoundException $e) {
			return;
		}

		$this->languageId = $language->getId();
	}

	/**
	 * Get the language id.
	 *
	 * @return LanguageId|null
	 */
	public function getLanguageId() : ?LanguageId
	{
		return $this->languageId;
	}
}
