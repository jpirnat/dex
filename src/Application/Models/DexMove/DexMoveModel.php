<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Application\Models\GenerationModel;
use Jp\Dex\Domain\Flags\FlagRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface;

final class DexMoveModel
{
	private GenerationModel $generationModel;
	private MoveRepositoryInterface $moveRepository;
	private MoveNameRepositoryInterface $moveNameRepository;
	private FlagRepositoryInterface $flagRepository;
	private DexVersionGroupRepositoryInterface $dexVgRepository;
	private DexMovePokemonModel $dexMovePokemonModel;


	/** @var array $move */
	private array $move = [];

	/** @var array $flags */
	private array $flags = [];

	/** @var DexVersionGroup[] $versionGroups */
	private array $versionGroups = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param MoveRepositoryInterface $moveRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param FlagRepositoryInterface $flagRepository
	 * @param DexVersionGroupRepositoryInterface $dexVgRepository
	 * @param DexMovePokemonModel $dexMovePokemonModel
	 */
	public function __construct(
		GenerationModel $generationModel,
		MoveRepositoryInterface $moveRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		FlagRepositoryInterface $flagRepository,
		DexVersionGroupRepositoryInterface $dexVgRepository,
		DexMovePokemonModel $dexMovePokemonModel
	) {
		$this->generationModel = $generationModel;
		$this->moveRepository = $moveRepository;
		$this->moveNameRepository = $moveNameRepository;
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
		$moveName = $this->moveNameRepository->getByLanguageAndMove(
			$languageId,
			$move->getId()
		);
		$this->move = [
			'identifier' => $move->getIdentifier(),
			'name' => $moveName->getName(),
		];

		// Set generations for the generation control.
		$this->generationModel->setWithMove($move->getId());

		// Set the move's flags.
		$allFlags = $this->flagRepository->getByGeneration($generationId, $languageId);
		$moveFlagIds = $this->flagRepository->getByMove($generationId, $move->getId());
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
			$move->getId(),
			$languageId,
			$generationId
		);

		$this->dexMovePokemonModel->setData(
			$move->getId(),
			$generationId,
			$languageId,
			$this->versionGroups
		);
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
	 * Get the dex move Pokémon model.
	 *
	 * @return DexMovePokemonModel
	 */
	public function getDexMovePokemonModel() : DexMovePokemonModel
	{
		return $this->dexMovePokemonModel;
	}

	/**
	 * Get the move.
	 *
	 * @return array
	 */
	public function getMove() : array
	{
		return $this->move;
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
}
