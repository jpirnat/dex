<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Categories\CategoryId;
use Jp\Dex\Domain\Flags\FlagRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\Inflictions\InflictionId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveType;
use Jp\Dex\Domain\Moves\VgMoveRepositoryInterface;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class DexMoveModel
{
	private DexMove $move;
	private array $detailedData = [];

	/** @var DexType[] $types */
	private array $types = [];

	/** @var float[] $damageDealt */
	private array $damageDealt = [];

	private array $statChanges = [];
	private array $flags = [];

	/** @var DexVersionGroup[] $versionGroups */
	private array $versionGroups = [];


	public function __construct(
		private VersionGroupModel $versionGroupModel,
		private MoveRepositoryInterface $moveRepository,
		private DexMoveRepositoryInterface $dexMoveRepository,
		private VgMoveRepositoryInterface $vgMoveRepository,
		private DexTypeRepositoryInterface $dexTypeRepository,
		private TypeMatchupRepositoryInterface $typeMatchupRepository,
		private FlagRepositoryInterface $flagRepository,
		private DexVersionGroupRepositoryInterface $dexVgRepository,
		private DexMovePokemonModel $dexMovePokemonModel,
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

		// Set the move's flags.
		$this->flags = [];
		$allFlags = $this->flagRepository->getByVersionGroup($versionGroupId, $languageId);
		$moveFlagIds = $this->flagRepository->getByMove($versionGroupId, $moveId);
		foreach ($allFlags as $flagId => $flag) {
			$has = isset($moveFlagIds[$flagId]); // Does the move have this flag?

			$this->flags[] = [
				'identifier' => $flag->getIdentifier(),
				'name' => $flag->getName(),
				'description' => $flag->getDescription(),
				'has' => $has,
			];
		}

		// Get the version groups this move has appeared in.
		$this->versionGroups = $this->dexVgRepository->getWithMove(
			$moveId,
			$languageId,
			$this->versionGroupModel->getVersionGroup()->getGenerationId(),
		);

		$this->dexMovePokemonModel->setData(
			$moveId,
			$this->versionGroupModel->getVersionGroup(),
			$languageId,
			$this->versionGroups,
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
			$this->versionGroupModel->getVersionGroup()->getGenerationId(),
			$vgMove->getTypeId(),
		);
		foreach ($attackingMatchups as $matchup) {
			$defendingTypeId = $matchup->getDefendingTypeId()->value();
			$defendingType = $this->types[$defendingTypeId];
			$identifier = $defendingType->getIdentifier();
			$this->damageDealt[$identifier] = $matchup->getMultiplier();
		}

		if ($moveId->value() === MoveId::FLYING_PRESS) {
			$attackingMatchups = $this->typeMatchupRepository->getByAttackingType(
				$this->versionGroupModel->getVersionGroup()->getGenerationId(),
				new TypeId(TypeId::FLYING),
			);
			foreach ($attackingMatchups as $matchup) {
				$defendingTypeId = $matchup->getDefendingTypeId()->value();
				$defendingType = $this->types[$defendingTypeId];
				$identifier = $defendingType->getIdentifier();
				$this->damageDealt[$identifier] *= $matchup->getMultiplier();
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


	/**
	 * Get the version group model.
	 */
	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	/**
	 * Get the move.
	 */
	public function getMove() : DexMove
	{
		return $this->move;
	}

	/**
	 * Get the detailed data.
	 */
	public function getDetailedData() : array
	{
		return $this->detailedData;
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
	 * Get the damage dealt matchups.
	 *
	 * @return float[]
	 */
	public function getDamageDealt() : array
	{
		return $this->damageDealt;
	}

	/**
	 * Get the stat changes.
	 */
	public function getStatChanges() : array
	{
		return $this->statChanges;
	}

	/**
	 * Get the flags.
	 */
	public function getFlags() : array
	{
		return $this->flags;
	}

	/**
	 * Get the version groups.
	 *
	 * @return DexVersionGroup[]
	 */
	public function getVersionGroups() : array
	{
		return $this->versionGroups;
	}

	/**
	 * Get the dex move Pokémon model.
	 */
	public function getDexMovePokemonModel() : DexMovePokemonModel
	{
		return $this->dexMovePokemonModel;
	}
}
