<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

class DexAbilitiesModel
{
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
	 * @param AbilityRepositoryInterface $abilityRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param AbilityDescriptionRepositoryInterface $abilityDescriptionRepository
	 */
	public function __construct(
		AbilityRepositoryInterface $abilityRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		AbilityDescriptionRepositoryInterface $abilityDescriptionRepository
	) {
		$this->abilityRepository = $abilityRepository;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->abilityDescriptionRepository = $abilityDescriptionRepository;
	}

	/**
	 * Set data for the /dex/abilities page.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(LanguageId $languageId) : void
	{
		$generationId = new GenerationId(7); // TODO

		$abilities = $this->abilityRepository->getAll();

		$abilityNames = $this->abilityNameRepository->getByLanguage($languageId);

		$abilityDescriptions = $this->abilityDescriptionRepository->getByGenerationAndLanguage(
			$generationId,
			$languageId
		);

		$this->abilities = [];

		foreach ($abilities as $ability) {
			$abilityId = $ability->getId()->value();

			$abilityName = $abilityNames[$abilityId];
			$abilityDescription = $abilityDescriptions[$abilityId];

			$this->abilities[] = [
				'identifier' => $ability->getIdentifier(),
				'name' => $abilityName->getName(),
				'description' => $abilityDescription->getDescription(),
			];
		}
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
