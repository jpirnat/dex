<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexAbilitiesModel
{
	private array $abilities = [];


	public function __construct(
		private GenerationModel $generationModel,
		private DexAbilityRepositoryInterface $dexAbilityRepository,
	) {}


	/**
	 * Set data for the dex abilities page.
	 */
	public function setData(
		string $generationIdentifier,
		LanguageId $languageId
	) : void {
		$generationId = $this->generationModel->setByIdentifier($generationIdentifier);

		$this->generationModel->setGensSince(new GenerationId(3));

		$this->abilities = $this->dexAbilityRepository->getByGeneration(
			$generationId,
			$languageId
		);
	}


	/**
	 * Get the generation model.
	 */
	public function getGenerationModel() : GenerationModel
	{
		return $this->generationModel;
	}

	/**
	 * Get the abilities.
	 */
	public function getAbilities() : array
	{
		return $this->abilities;
	}
}
