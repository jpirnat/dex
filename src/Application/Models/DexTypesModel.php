<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\TypeIcons\TypeIconRepositoryInterface;
use Jp\Dex\Domain\Types\TypeEffectivenessRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

class DexTypesModel
{
	/** @var GenerationModel $generationModel */
	private $generationModel;

	/** @var TypeRepositoryInterface $typeRepository */
	private $typeRepository;

	/** @var TypeIconRepositoryInterface $typeIconRepository */
	private $typeIconRepository;

	/** @var TypeEffectivenessRepositoryInterface $typeEffectivenessRepository */
	private $typeEffectivenessRepository;


	/** @var array $types */
	private $types;

	/** @var int[][] $factors */
	private $factors = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param TypeRepositoryInterface $typeRepository
	 * @param TypeIconRepositoryInterface $typeIconRepository
	 * @param TypeEffectivenessRepositoryInterface $typeEffectivenessRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		TypeRepositoryInterface $typeRepository,
		TypeIconRepositoryInterface $typeIconRepository,
		TypeEffectivenessRepositoryInterface $typeEffectivenessRepository
	) {
		$this->generationModel = $generationModel;
		$this->typeRepository = $typeRepository;
		$this->typeIconRepository = $typeIconRepository;
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
		$generationId = $this->generationModel->setGeneration($generationIdentifier);

		$this->generationModel->setGensSince(new GenerationId(1));

		$types = $this->typeRepository->getMainByGeneration($generationId);

		$typeIcons = $this->typeIconRepository->getByGenerationAndLanguage(
			$generationId,
			$languageId
		);

		// Consolidate data for each type.
		$this->types = [];
		foreach ($types as $type) {
			$typeIcon = $typeIcons[$type->getId()->value()];

			$this->types[] = [
				'id' => $type->getId()->value(),
				'identifier' => $type->getIdentifier(),
				'icon' => $typeIcon->getImage(),
			];
		}

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
	 * @return array
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
