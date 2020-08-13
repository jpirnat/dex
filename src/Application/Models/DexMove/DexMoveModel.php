<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Application\Models\GenerationModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface;

final class DexMoveModel
{
	private GenerationModel $generationModel;
	private DexMovePokemonModel $dexMovePokemonModel;
	private MoveRepositoryInterface $moveRepository;
	private MoveNameRepositoryInterface $moveNameRepository;
	private DexVersionGroupRepositoryInterface $dexVgRepository;


	/** @var array $move */
	private array $move = [];

	/** @var DexVersionGroup[] $versionGroups */
	private array $versionGroups = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param DexMovePokemonModel $dexMovePokemonModel
	 * @param MoveRepositoryInterface $moveRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param DexVersionGroupRepositoryInterface $dexVgRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		DexMovePokemonModel $dexMovePokemonModel,
		MoveRepositoryInterface $moveRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		DexVersionGroupRepositoryInterface $dexVgRepository
	) {
		$this->generationModel = $generationModel;
		$this->dexMovePokemonModel = $dexMovePokemonModel;
		$this->moveRepository = $moveRepository;
		$this->moveNameRepository = $moveNameRepository;
		$this->dexVgRepository = $dexVgRepository;
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
	 * Get the version groups.
	 *
	 * @return DexVersionGroup[]
	 */
	public function getVersionGroups() : array
	{
		return $this->versionGroups;
	}
}
