<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Categories\CategoryId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\Flags\MoveFlagRepositoryInterface;
use Jp\Dex\Domain\Moves\Inflictions\InflictionId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveType;
use Jp\Dex\Domain\Moves\VgMoveRepositoryInterface;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeIdentifier;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class DexMoveModel
{
	private(set) DexMove $move;
	private(set) array $detailedData = [];

	/** @var DexType[] $types */
	private(set) array $types = [];

	/** @var float[] $damageDealt */
	private(set) array $damageDealt = [];

	private(set) array $statChanges = [];
	private(set) array $flags = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly MoveRepositoryInterface $moveRepository,
		private readonly DexMoveRepositoryInterface $dexMoveRepository,
		private readonly VgMoveRepositoryInterface $vgMoveRepository,
		private readonly DexTypeRepositoryInterface $dexTypeRepository,
		private readonly TypeMatchupRepositoryInterface $typeMatchupRepository,
		private readonly MoveFlagRepositoryInterface $flagRepository,
		private(set) readonly DexMovePokemonModel $dexMovePokemonModel,
	) {}


	/**
	 * Set data for the dex move page.
	 */
	public function setData(
		string $vgIdentifier,
		string $moveIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$move = $this->moveRepository->getByIdentifier($moveIdentifier);

		// Set version groups for the version group control.
		$this->versionGroupModel->setWithMove($move->id);

		$this->move = $this->dexMoveRepository->getById($versionGroupId, $move->id, $languageId);

		// Set the move's detailed data.
		$this->setDetailedData($versionGroupId, $move->id, $languageId);

		if ($move->type->value === MoveType::Z_MOVE) {
			$zMoveImage = $this->vgMoveRepository->getZMoveImage($move->id, $languageId);
			$this->detailedData['zMoveImage'] = $zMoveImage;
		}

		// Set the type matchups.
		$this->setMatchups($versionGroupId, $move->id, $languageId);

		$this->statChanges = $this->vgMoveRepository->getStatChanges(
			$versionGroupId,
			$move->id,
			$languageId,
		);

		$this->setFlags($versionGroupId, $move->id, $languageId);

		$this->dexMovePokemonModel->setData(
			$versionGroupId,
			$move->id,
			$languageId,
		);
	}

	/**
	 * Set the move's detailed data.
	 */
	public function setDetailedData(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : void {
		$vgMove = $this->vgMoveRepository->getByVgAndMove($versionGroupId, $moveId);

		$infliction = null;
		if ($vgMove->inflictionId->value !== InflictionId::NONE) {
			$infliction = $this->vgMoveRepository->getInfliction(
				$vgMove->inflictionId,
				$languageId,
			);
			$infliction['percent'] = $vgMove->inflictionPercent;
		}

		$target = $this->vgMoveRepository->getTarget(
			$vgMove->targetId,
			$languageId,
		);

		$zMove = null;
		if ($vgMove->zMoveId !== null) {
			$zMove = $this->vgMoveRepository->getZMove(
				$vgMove->zMoveId,
				$languageId,
			);
			$zMove['power'] = $vgMove->zBasePower;
		}

		if ($vgMove->zPowerEffectId !== null) {
			$zPowerEffect = $this->vgMoveRepository->getZPowerEffect(
				$vgMove->zPowerEffectId,
				$languageId,
			);
			$zMove['zPowerEffect'] = $zPowerEffect;
		}

		$maxMove = null;
		if ($vgMove->maxMoveId !== null) {
			$maxMove = $this->vgMoveRepository->getMaxMove(
				$vgMove->maxMoveId,
				$languageId,
			);
			$maxMove['power'] = $vgMove->maxPower;
		}

		$this->detailedData = [
			'priority' => $vgMove->priority,
			'minHits' => $vgMove->minHits,
			'maxHits' => $vgMove->maxHits,
			'infliction' => $infliction,
			'minTurns' => $vgMove->minTurns,
			'maxTurns' => $vgMove->maxTurns,
			'critStage' => $vgMove->critStage,
			'flinchPercent' => $vgMove->flinchPercent,
			'effect' => $vgMove->effect,
			'effectPercent' => $vgMove->effectPercent,
			'recoilPercent' => $vgMove->recoilPercent,
			'healPercent' => $vgMove->healPercent,
			'target' => $target,
			'zMove' => $zMove,
			'maxMove' => $maxMove,
		];
	}

	/**
	 * Set the move's type matchups.
	 */
	private function setMatchups(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : void {
		$this->types = [];
		$this->damageDealt = [];

		$vgMove = $this->vgMoveRepository->getByVgAndMove($versionGroupId, $moveId);
		if ($vgMove->categoryId->value === CategoryId::STATUS) {
			// This move doesn't do damage. No matchups needed.
			return;
		}

		$this->types = $this->dexTypeRepository->getMainByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		$attackingMatchups = $this->typeMatchupRepository->getByAttackingType(
			$this->versionGroupModel->versionGroup->generationId,
			$vgMove->typeId,
		);
		foreach ($attackingMatchups as $matchup) {
			$defendingTypeIdentifier = $matchup->defendingTypeIdentifier;
			$this->damageDealt[$defendingTypeIdentifier] = $matchup->multiplier;
		}

		if ($moveId->value === MoveId::FLYING_PRESS) {
			$attackingMatchups = $this->typeMatchupRepository->getByAttackingType(
				$this->versionGroupModel->versionGroup->generationId,
				new TypeId(TypeId::FLYING),
			);
			foreach ($attackingMatchups as $matchup) {
				$defendingTypeIdentifier = $matchup->defendingTypeIdentifier;
				$this->damageDealt[$defendingTypeIdentifier] *= $matchup->multiplier;
			}
		}

		if ($moveId->value === MoveId::FREEZE_DRY) {
			$this->damageDealt[TypeIdentifier::WATER] = 2;
		}

		if ($moveId->value === MoveId::THOUSAND_ARROWS) {
			$this->damageDealt[TypeIdentifier::FLYING] = 1;
		}
	}

	private function setFlags(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : void {
		$this->flags = [];

		$allFlags = $this->flagRepository->getByVersionGroupSingular(
			$versionGroupId,
			$languageId,
		);
		$moveFlagIds = $this->flagRepository->getByMove(
			$versionGroupId,
			$moveId,
		);

		foreach ($allFlags as $flagId => $flag) {
			$has = isset($moveFlagIds[$flagId]); // Does the move have this flag?

			$this->flags[] = [
				'identifier' => $flag->identifier,
				'name' => $flag->name,
				'description' => $flag->description,
				'has' => $has,
			];
		}
	}
}
