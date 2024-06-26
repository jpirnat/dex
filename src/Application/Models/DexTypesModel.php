<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexTypesModel
{
	private array $types = [];

	/** @var int[][] $multipliers */
	private array $multipliers = [];


	public function __construct(
		private VersionGroupModel $versionGroupModel,
		private DexTypeRepositoryInterface $dexTypeRepository,
		private TypeRepositoryInterface $typeRepository,
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

		$dexTypes = $this->dexTypeRepository->getMainByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		$types = $this->typeRepository->getMainByVersionGroup($versionGroupId);
		foreach ($dexTypes as $dexType) {
			$type = $types[$dexType->getId()->value()];
			$this->types[] = [
				'id' => $dexType->getId()->value(),
				'identifier' => $dexType->getIdentifier(),
				'name' => $dexType->getName(),
				'symbolIcon' => $type->getSymbolIcon(),
				'nameIcon' => $dexType->getIcon(),
			];
		}

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
	 * Get the version group model.
	 */
	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	/**
	 * Get the types.
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
