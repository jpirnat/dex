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
		$moveId = $move->getId();

		// Set version groups for the version group control.
		$this->versionGroupModel->setWithMove($moveId);

		$this->move = $this->dexMoveRepository->getById($versionGroupId, $moveId, $languageId);

		// Set the move's detailed data.
		$this->setDetailedData($versionGroupId, $moveId, $languageId);

		if ($move->getType()->value() === MoveType::Z_MOVE) {
			$zMoveImage = $this->vgMoveRepository->getZMoveImage($moveId, $languageId);
			$this->detailedData['zMoveImage'] = $zMoveImage;
		}

		// Set the type matchups.
		$this->setMatchups($versionGroupId, $moveId, $languageId);

		$this->statChanges = $this->vgMoveRepository->getStatChanges(
			$versionGroupId,
			$moveId,
			$languageId,
		);

		$this->setFlags($versionGroupId, $moveId, $languageId);

		$this->dexMovePokemonModel->setData(
			$versionGroupId,
			$moveId,
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
		if ($vgMove->getInflictionId()->value() !== InflictionId::NONE) {
			$infliction = $this->vgMoveRepository->getInfliction(
				$vgMove->getInflictionId(),
				$languageId,
			);
			$infliction['percent'] = $vgMove->getInflictionPercent();
		}

		$target = $this->vgMoveRepository->getTarget(
			$vgMove->getTargetId(),
			$languageId,
		);

		$zMove = null;
		if ($vgMove->getZMoveId() !== null) {
			$zMove = $this->vgMoveRepository->getZMove(
				$vgMove->getZMoveId(),
				$languageId,
			);
			$zMove['power'] = $vgMove->getZBasePower();
		}

		if ($vgMove->getZPowerEffectId() !== null) {
			$zPowerEffect = $this->vgMoveRepository->getZPowerEffect(
				$vgMove->getZPowerEffectId(),
				$languageId,
			);
			$zMove['zPowerEffect'] = $zPowerEffect;
		}

		$maxMove = null;
		if ($vgMove->getMaxMoveId() !== null) {
			$maxMove = $this->vgMoveRepository->getMaxMove(
				$vgMove->getMaxMoveId(),
				$languageId,
			);
			$maxMove['power'] = $vgMove->getMaxPower();
		}

		$this->detailedData = [
			'priority' => $vgMove->getPriority(),
			'minHits' => $vgMove->getMinHits(),
			'maxHits' => $vgMove->getMaxHits(),
			'infliction' => $infliction,
			'minTurns' => $vgMove->getMinTurns(),
			'maxTurns' => $vgMove->getMaxTurns(),
			'critStage' => $vgMove->getCritStage(),
			'flinchPercent' => $vgMove->getFlinchPercent(),
			'effect' => $vgMove->getEffect(),
			'effectPercent' => $vgMove->getEffectPercent(),
			'recoilPercent' => $vgMove->getRecoilPercent(),
			'healPercent' => $vgMove->getHealPercent(),
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
		if ($vgMove->getCategoryId()->value() === CategoryId::STATUS) {
			// This move doesn't do damage. No matchups needed.
			return;
		}

		$this->types = $this->dexTypeRepository->getMainByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		$attackingMatchups = $this->typeMatchupRepository->getByAttackingType(
			$this->versionGroupModel->versionGroup->getGenerationId(),
			$vgMove->getTypeId(),
		);
		foreach ($attackingMatchups as $matchup) {
			$defendingTypeIdentifier = $matchup->getDefendingTypeIdentifier();
			$this->damageDealt[$defendingTypeIdentifier] = $matchup->getMultiplier();
		}

		if ($moveId->value() === MoveId::FLYING_PRESS) {
			$attackingMatchups = $this->typeMatchupRepository->getByAttackingType(
				$this->versionGroupModel->versionGroup->getGenerationId(),
				new TypeId(TypeId::FLYING),
			);
			foreach ($attackingMatchups as $matchup) {
				$defendingTypeIdentifier = $matchup->getDefendingTypeIdentifier();
				$this->damageDealt[$defendingTypeIdentifier] *= $matchup->getMultiplier();
			}
		}

		if ($moveId->value() === MoveId::FREEZE_DRY) {
			// TODO: get this type identifier from somewhere else.
			$this->damageDealt['water'] = 2;
		}

		if ($moveId->value() === MoveId::THOUSAND_ARROWS) {
			// TODO: get this type identifier from somewhere else.
			$this->damageDealt['flying'] = 1;
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
				'identifier' => $flag->getIdentifier(),
				'name' => $flag->getName(),
				'description' => $flag->getDescription(),
				'has' => $has,
			];
		}
	}
}
