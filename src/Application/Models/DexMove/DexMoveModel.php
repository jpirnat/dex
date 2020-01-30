<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Application\Models\GenerationModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class DexMoveModel
{
	private GenerationModel $generationModel;
	private DexMovePokemonModel $dexMovePokemonModel;
	private MoveRepositoryInterface $moveRepository;
	private MoveNameRepositoryInterface $moveNameRepository;
	private VersionGroupRepositoryInterface $vgRepository;


	/** @var array $move */
	private array $move = [];

	/** @var VersionGroup[] $versionGroups */
	private array $versionGroups = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param DexMovePokemonModel $dexMovePokemonModel
	 * @param MoveRepositoryInterface $moveRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param VersionGroupRepositoryInterface $vgRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		DexMovePokemonModel $dexMovePokemonModel,
		MoveRepositoryInterface $moveRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		VersionGroupRepositoryInterface $vgRepository
	) {
		$this->generationModel = $generationModel;
		$this->dexMovePokemonModel = $dexMovePokemonModel;
		$this->moveRepository = $moveRepository;
		$this->moveNameRepository = $moveNameRepository;
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
		$moveName = $this->moveNameRepository->getByLanguageAndMove(
			$languageId,
			$move->getId()
		);
		$this->move = [
			'identifier' => $move->getIdentifier(),
			'name' => $moveName->getName(),
		];

		// Set generations for the generation control.
		$introducedInVgId = $move->getIntroducedInVersionGroupId();
		$this->generationModel->setGensSinceVg($introducedInVgId);

		// Get the version groups since this move was introduced.
		$introducedInVg = $this->vgRepository->getById($introducedInVgId);
		$this->versionGroups = $this->vgRepository->getBetween(
			$introducedInVg->getGenerationId(),
			$generationId
		);

		// Use the appropriate set of gen 1 games for the language.
		if ($languageId->isJapanese()) {
			unset($this->versionGroups[VersionGroupId::RED_BLUE]);
		} else {
			unset($this->versionGroups[VersionGroupId::RED_GREEN]);
			unset($this->versionGroups[VersionGroupId::BLUE]);
		}
		// Don't include Let's Go Pikachu/Eevee (yet).
		unset($this->versionGroups[19]);

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
	 * @return VersionGroup[]
	 */
	public function getVersionGroups() : array
	{
		return $this->versionGroups;
	}
}
