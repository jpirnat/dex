<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeEffectivenessRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexTypesModel
{
	private GenerationModel $generationModel;
	private DexTypeRepositoryInterface $dexTypeRepository;
	private TypeEffectivenessRepositoryInterface $typeEffectivenessRepository;


	/** @var DexType[] $types */
	private array $types = [];

	/** @var int[][] $factors */
	private array $factors = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param DexTypeRepositoryInterface $dexTypeRepository
	 * @param TypeEffectivenessRepositoryInterface $typeEffectivenessRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		DexTypeRepositoryInterface $dexTypeRepository,
		TypeEffectivenessRepositoryInterface $typeEffectivenessRepository
	) {
		$this->generationModel = $generationModel;
		$this->dexTypeRepository = $dexTypeRepository;
		$this->typeEffectivenessRepository = $typeEffectivenessRepository;
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
		$typeEffectivenesses = $this->typeEffectivenessRepository->getByGeneration(
			$generationId
		);
		$this->factors = [];
		foreach ($typeEffectivenesses as $typeEffectiveness) {
			$attackingTypeId = $typeEffectiveness->getAttackingTypeId()->value();
			$defendingTypeId = $typeEffectiveness->getDefendingTypeId()->value();
			$factor = $typeEffectiveness->getFactor();

			$this->factors[$attackingTypeId][$defendingTypeId] = $factor;
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
	 * Get the factors.
	 *
	 * @return float[][]
	 */
	public function getFactors() : array
	{
		return $this->factors;
	}
}
