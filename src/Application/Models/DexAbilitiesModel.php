<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexAbilitiesModel
{
	/** @var GenerationModel $generationModel */
	private $generationModel;

	/** @var AbilityRepositoryInterface $abilityRepository */
	private $abilityRepository;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/** @var AbilityDescriptionRepositoryInterface $abilityDescriptionRepository */
	private $abilityDescriptionRepository;


	/** @var array $abilities */
	private $abilities;


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param AbilityRepositoryInterface $abilityRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param AbilityDescriptionRepositoryInterface $abilityDescriptionRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		AbilityRepositoryInterface $abilityRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		AbilityDescriptionRepositoryInterface $abilityDescriptionRepository
	) {
		$this->generationModel = $generationModel;
		$this->abilityRepository = $abilityRepository;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->abilityDescriptionRepository = $abilityDescriptionRepository;
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

		$abilities = $this->abilityRepository->getByGeneration($generationId);

		$abilityNames = $this->abilityNameRepository->getByLanguage($languageId);

		$abilityDescriptions = $this->abilityDescriptionRepository->getByGenerationAndLanguage(
			$generationId,
			$languageId
		);

		$this->abilities = [];

		foreach ($abilities as $ability) {
			$abilityId = $ability->getId()->value();

			$abilityName = $abilityNames[$abilityId];
			$abilityDescription = $abilityDescriptions[$abilityId] ?? null;
			$abilityDescription = $abilityDescription
				? $abilityDescription->getDescription()
				: '-';

			$this->abilities[] = [
				'identifier' => $ability->getIdentifier(),
				'name' => $abilityName->getName(),
				'description' => $abilityDescription,
			];
		}
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
