<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexAbilitiesModel
{
	private GenerationModel $generationModel;
	private DexAbilityRepositoryInterface $dexAbilityRepository;


	private array $abilities = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param DexAbilityRepositoryInterface $dexAbilityRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		DexAbilityRepositoryInterface $dexAbilityRepository
	) {
		$this->generationModel = $generationModel;
		$this->dexAbilityRepository = $dexAbilityRepository;
	}

	/**
	 * Set data for the dex abilities page.
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
		$generationId = $this->generationModel->setByIdentifier($generationIdentifier);

		$this->generationModel->setGensSince(new GenerationId(3));

		$this->abilities = $this->dexAbilityRepository->getByGeneration(
			$generationId,
			$languageId
		);
	}

	/**
	 * Get the generation model.
	 *
	 * @return GenerationModel
	 */
	public function getGenerationModel() : GenerationModel
	{
		return $this->generationModel;
	}

	/**
	 * Get the abilities.
	 *
	 * @return array
	 */
	public function getAbilities() : array
	{
		return $this->abilities;
	}
}
