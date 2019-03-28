<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Application\Models\GenerationModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

class DexMoveModel
{
	/** @var GenerationModel $generationModel */
	private $generationModel;

	/** @var DexMovePokemonModel $dexMovePokemonModel */
	private $dexMovePokemonModel;

	/** @var MoveRepositoryInterface $moveRepository */
	private $moveRepository;

	/** @var VersionGroupRepositoryInterface $vgRepository */
	private $vgRepository;


	/** @var VersionGroup[] $versionGroups */
	private $versionGroups = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param DexMovePokemonModel $dexMovePokemonModel
	 * @param MoveRepositoryInterface $moveRepository
	 * @param VersionGroupRepositoryInterface $vgRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		DexMovePokemonModel $dexMovePokemonModel,
		MoveRepositoryInterface $moveRepository,
		VersionGroupRepositoryInterface $vgRepository
	) {
		$this->generationModel = $generationModel;
		$this->dexMovePokemonModel = $dexMovePokemonModel;
		$this->moveRepository = $moveRepository;
		$this->vgRepository = $vgRepository;
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

		// Set generations for the generation control.
		$introducedInVgId = $move->getIntroducedInVersionGroupId();
		$this->generationModel->setGensSinceVg($introducedInVgId);

		// Get the version groups since this move was introduced.
		$introducedInVg = $this->vgRepository->getById($introducedInVgId);
		$this->versionGroups = $this->vgRepository->getBetween(
			$introducedInVg->getGenerationId(),
			$generationId
		);

		$this->dexMovePokemonModel->setData(
			$move->getId(),
			$generationId,
			$languageId
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
	 * Get the version groups.
	 *
	 * @return VersionGroup[]
	 */
	public function getVersionGroups() : array
	{
		return $this->versionGroups;
	}
}
