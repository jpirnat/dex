<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Application\Models\GenerationModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class DexPokemonModel
{
	private GenerationModel $generationModel;
	private DexPokemonMovesModel $dexPokemonMovesModel;
	private PokemonRepositoryInterface $pokemonRepository;
	private PokemonNameRepositoryInterface $pokemonNameRepository;
	private VersionGroupRepositoryInterface $vgRepository;


	private array $pokemon = [];

	/** @var VersionGroup[] $versionGroups */
	private array $versionGroups = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param DexPokemonMovesModel $dexPokemonMovesModel
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param VersionGroupRepositoryInterface $vgRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		DexPokemonMovesModel $dexPokemonMovesModel,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		VersionGroupRepositoryInterface $vgRepository
	) {
		$this->generationModel = $generationModel;
		$this->dexPokemonMovesModel = $dexPokemonMovesModel;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->vgRepository = $vgRepository;
	}


	/**
	 * Set data for the dex Pokémon page.
	 *
	 * @param string $generationIdentifier
	 * @param string $pokemonIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $generationIdentifier,
		string $pokemonIdentifier,
		LanguageId $languageId
	) : void {
		$generationId = $this->generationModel->setByIdentifier($generationIdentifier);

		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemon->getId()
		);
		$this->pokemon = [
			'identifier' => $pokemon->getIdentifier(),
			'name' => $pokemonName->getName(),
		];

		// Set generations for the generation control.
		$introducedInVgId = $pokemon->getIntroducedInVersionGroupId();
		$this->generationModel->setGensSinceVg($introducedInVgId);

		// Get the version groups since this Pokémon was introduced.
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

		$this->dexPokemonMovesModel->setData(
			$pokemon->getId(),
			$introducedInVg->getGenerationId(),
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
	 * Get the dex Pokémon moves model.
	 *
	 * @return DexPokemonMovesModel
	 */
	public function getDexPokemonMovesModel() : DexPokemonMovesModel
	{
		return $this->dexPokemonMovesModel;
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return array
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
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
