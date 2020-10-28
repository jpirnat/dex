<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Natures\DexNatureRepositoryInterface;

final class DexNaturesModel
{
	private string $generationIdentifier;
	private array $natures = [];


	/**
	 * Constructor.
	 *
	 * @param DexNatureRepositoryInterface $dexNatureRepository
	 */
	public function __construct(
		private DexNatureRepositoryInterface $dexNatureRepository,
	) {}


	/**
	 * Set data for the dex natures page.
	 *
	 * @param string $generationIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $generationIdentifier,
		LanguageId $languageId
	) : void {
		$this->generationIdentifier = $generationIdentifier;

		$this->natures = $this->dexNatureRepository->getByLanguage($languageId);
	}

	/**
	 * Get the generation identifier.
	 *
	 * @return string
	 */
	public function getGenerationIdentifier() : string
	{
		return $this->generationIdentifier;
	}

	/**
	 * Get the natures.
	 *
	 * @return array
	 */
	public function getNatures() : array
	{
		return $this->natures;
	}
}
