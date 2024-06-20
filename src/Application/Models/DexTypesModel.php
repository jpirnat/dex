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
	/** @var DexType[] $types */
	private array $types = [];

	/** @var int[][] $multipliers */
	private array $multipliers = [];


	public function __construct(
		private VersionGroupModel $versionGroupModel,
		private DexTypeRepositoryInterface $dexTypeRepository,
		private TypeMatchupRepositoryInterface $typeMatchupRepository,
	) {}


	/**
	 * Set data for the dex types page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setSinceGeneration(new GenerationId(1));

		$this->types = $this->dexTypeRepository->getMainByVersionGroup(
			$versionGroupId,
			$languageId,
		);

		// Get this generation's type chart.
		$typeMatchups = $this->typeMatchupRepository->getByGeneration(
			$this->versionGroupModel->getVersionGroup()->getGenerationId()
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
	 */
	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
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
