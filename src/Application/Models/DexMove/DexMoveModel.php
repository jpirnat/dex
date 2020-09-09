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
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexMoveModel
{
	private GenerationModel $generationModel;
	private MoveRepositoryInterface $moveRepository;
	private DexMoveRepositoryInterface $dexMoveRepository;
	private GenerationMoveRepositoryInterface $generationMoveRepository;
	private DexTypeRepositoryInterface $dexTypeRepository;
	private TypeMatchupRepositoryInterface $typeMatchupRepository;
	private FlagRepositoryInterface $flagRepository;
	private DexVersionGroupRepositoryInterface $dexVgRepository;
	private DexMovePokemonModel $dexMovePokemonModel;


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


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param MoveRepositoryInterface $moveRepository
	 * @param DexMoveRepositoryInterface $dexMoveRepository
	 * @param GenerationMoveRepositoryInterface $generationMoveRepository
	 * @param DexTypeRepositoryInterface $dexTypeRepository
	 * @param TypeMatchupRepositoryInterface $typeMatchupRepository
	 * @param FlagRepositoryInterface $flagRepository
	 * @param DexVersionGroupRepositoryInterface $dexVgRepository
	 * @param DexMovePokemonModel $dexMovePokemonModel
	 */
	public function __construct(
		GenerationModel $generationModel,
		MoveRepositoryInterface $moveRepository,
		DexMoveRepositoryInterface $dexMoveRepository,
		GenerationMoveRepositoryInterface $generationMoveRepository,
		DexTypeRepositoryInterface $dexTypeRepository,
		TypeMatchupRepositoryInterface $typeMatchupRepository,
		FlagRepositoryInterface $flagRepository,
		DexVersionGroupRepositoryInterface $dexVgRepository,
		DexMovePokemonModel $dexMovePokemonModel
	) {
		$this->generationModel = $generationModel;
		$this->moveRepository = $moveRepository;
		$this->dexMoveRepository = $dexMoveRepository;
		$this->generationMoveRepository = $generationMoveRepository;
		$this->dexTypeRepository = $dexTypeRepository;
		$this->typeMatchupRepository = $typeMatchupRepository;
		$this->flagRepository = $flagRepository;
		$this->dexVgRepository = $dexVgRepository;
		$this->dexMovePokemonModel = $dexMovePokemonModel;
	}


	/**
	 * Set data for the dex move page.
	 *
	 * @param string $generationIdentifier
	 * @param string $moveIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
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
	 *
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return void
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

		$zPowerEffect = null;
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
	 *
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return void
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
	 * Get the move.
	 *
	 * @return DexMove
	 */
	public function getMove() : DexMove
	{
		return $this->move;
	}

	/**
	 * Get the detailed data.
	 *
	 * @return array
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
	 *
	 * @return array
	 */
	public function getStatChanges() : array
	{
		return $this->statChanges;
	}

	/**
	 * Get the flags.
	 *
	 * @return array
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
	 *
	 * @return DexMovePokemonModel
	 */
	public function getDexMovePokemonModel() : DexMovePokemonModel
	{
		return $this->dexMovePokemonModel;
	}
}
