<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Application\Models\GenerationModel;
use Jp\Dex\Domain\Categories\CategoryId;
use Jp\Dex\Domain\Flags\FlagRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\GenerationMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveType;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

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
		private GenerationModel $generationModel,
		private MoveRepositoryInterface $moveRepository,
		private DexMoveRepositoryInterface $dexMoveRepository,
		private GenerationMoveRepositoryInterface $generationMoveRepository,
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
		string $generationIdentifier,
		string $moveIdentifier,
		LanguageId $languageId
	) : void {
		$generationId = $this->generationModel->setByIdentifier(
			$generationIdentifier
		);

		$move = $this->moveRepository->getByIdentifier($moveIdentifier);
		$moveId = $move->getId();

		// Set generations for the generation control.
		$this->generationModel->setWithMove($moveId);

		$this->move = $this->dexMoveRepository->getById($generationId, $moveId, $languageId);

		// Set the move's detailed data.
		$this->setDetailedData($generationId, $moveId, $languageId);

		if ($move->getType()->value() === MoveType::Z_MOVE) {
			$zMoveImage = $this->generationMoveRepository->getZMoveImage($moveId, $languageId);
			$this->detailedData['zMoveImage'] = $zMoveImage;
		}

		// Set the type matchups.
		$this->setMatchups($generationId, $moveId, $languageId);

		$this->statChanges = $this->generationMoveRepository->getStatChanges(
			$generationId,
			$moveId,
			$languageId
		);

		// Set the move's flags.
		$this->flags = [];
		$allFlags = $this->flagRepository->getByGeneration($generationId, $languageId);
		$moveFlagIds = $this->flagRepository->getByMove($generationId, $moveId);
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
			$generationId
		);

		$this->dexMovePokemonModel->setData(
			$moveId,
			$generationId,
			$languageId,
			$this->versionGroups
		);
	}

	/**
	 * Set the move's detailed data.
	 */
	public function setDetailedData(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : void {
		$generationMove = $this->generationMoveRepository->getByGenerationAndMove(
			$generationId,
			$moveId
		);

		$infliction = null;
		if ($generationMove->getInflictionId() !== null) {
			$infliction = $this->generationMoveRepository->getInfliction(
				$generationMove->getInflictionId(),
				$languageId
			);
			$infliction['percent'] = $generationMove->getInflictionPercent();
		}

		$target = $this->generationMoveRepository->getTarget(
			$generationMove->getTargetId(),
			$languageId
		);

		$zMove = null;
		if ($generationMove->getZMoveId() !== null) {
			$zMove = $this->generationMoveRepository->getZMove(
				$generationMove->getZMoveId(),
				$languageId
			);
			$zMove['power'] = $generationMove->getZBasePower();
		}

		if ($generationMove->getZPowerEffectId() !== null) {
			$zPowerEffect = $this->generationMoveRepository->getZPowerEffect(
				$generationMove->getZPowerEffectId(),
				$languageId
			);
			$zMove['zPowerEffect'] = $zPowerEffect;
		}

		$maxMove = null;
		if ($generationMove->getMaxMoveId() !== null) {
			$maxMove = $this->generationMoveRepository->getMaxMove(
				$generationMove->getMaxMoveId(),
				$languageId
			);
			$maxMove['power'] = $generationMove->getMaxPower();
		}

		$this->detailedData = [
			'priority' => $generationMove->getPriority(),
			'minHits' => $generationMove->getMinHits(),
			'maxHits' => $generationMove->getMaxHits(),
			'infliction' => $infliction,
			'minTurns' => $generationMove->getMinTurns(),
			'maxTurns' => $generationMove->getMaxTurns(),
			'critStage' => $generationMove->getCritStage(),
			'flinchPercent' => $generationMove->getFlinchPercent(),
			'effect' => $generationMove->getEffect(),
			'effectPercent' => $generationMove->getEffectPercent(),
			'recoilPercent' => $generationMove->getRecoilPercent(),
			'healPercent' => $generationMove->getHealPercent(),
			'target' => $target,
			'zMove' => $zMove,
			'maxMove' => $maxMove,
		];
	}

	/**
	 * Set the move's type matchups.
	 */
	private function setMatchups(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : void {
		$this->types = [];
		$this->damageDealt = [];

		$generationMove = $this->generationMoveRepository->getByGenerationAndMove(
			$generationId,
			$moveId
		);
		if ($generationMove->getCategoryId()->value() === CategoryId::STATUS) {
			// This move doesn't do damage. No matchups needed.
			return;
		}

		$this->types = $this->dexTypeRepository->getMainByGeneration(
			$generationId,
			$languageId
		);
		$attackingMatchups = $this->typeMatchupRepository->getByAttackingType(
			$generationId,
			$generationMove->getTypeId()
		);
		foreach ($attackingMatchups as $matchup) {
			$defendingTypeId = $matchup->getDefendingTypeId()->value();
			$defendingType = $this->types[$defendingTypeId];
			$identifier = $defendingType->getIdentifier();
			$this->damageDealt[$identifier] = $matchup->getMultiplier();
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
	 * Get the generation model.
	 */
	public function getGenerationModel() : GenerationModel
	{
		return $this->generationModel;
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
