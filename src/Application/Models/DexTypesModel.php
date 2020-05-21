<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexTypesModel
{
	private GenerationModel $generationModel;
	private DexTypeRepositoryInterface $dexTypeRepository;
	private TypeMatchupRepositoryInterface $typeMatchupRepository;


	/** @var DexType[] $types */
	private array $types = [];

	/** @var int[][] $multipliers */
	private array $multipliers = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param DexTypeRepositoryInterface $dexTypeRepository
	 * @param TypeMatchupRepositoryInterface $typeMatchupRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		DexTypeRepositoryInterface $dexTypeRepository,
		TypeMatchupRepositoryInterface $typeMatchupRepository
	) {
		$this->generationModel = $generationModel;
		$this->dexTypeRepository = $dexTypeRepository;
		$this->typeMatchupRepository = $typeMatchupRepository;
	}

	/**
	 * Set data for the dex types page.
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

		$this->generationModel->setGensSince(new GenerationId(1));

		$this->types = $this->dexTypeRepository->getMainByGeneration(
			$generationId,
			$languageId
		);

		// Get this generation's type chart.
		$typeMatchups = $this->typeMatchupRepository->getByGeneration(
			$generationId
		);
		$this->multipliers = [];
		foreach ($typeMatchups as $typeMatchup) {
			$attackingTypeId = $typeMatchup->getAttackingTypeId()->value();
			$defendingTypeId = $typeMatchup->getDefendingTypeId()->value();
			$multiplier = $typeMatchup->getMultiplier();

			$this->multipliers[$attackingTypeId][$defendingTypeId] = $multiplier;
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
	 * Get the types.
	 *
	 * @return DexType[]
	 */
	public function getTypes() : array
	{
		return $this->types;
	}

	/**
	 * Get the multipliers.
	 *
	 * @return float[][]
	 */
	public function getMultipliers() : array
	{
		return $this->multipliers;
	}
}
