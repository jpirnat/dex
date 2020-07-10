<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\BreedingChains;

use Jp\Dex\Application\Models\GenerationModel;
use Jp\Dex\Domain\BreedingChains\BreedingChainFinder;
use Jp\Dex\Domain\EggGroups\EggGroupNameRepositoryInterface;
use Jp\Dex\Domain\EggGroups\PokemonEggGroupRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\PokemonMove;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveFormatter;
use Jp\Dex\Domain\Species\SpeciesRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class BreedingChainsModel
{
	private GenerationModel $generationModel;
	private PokemonRepositoryInterface $pokemonRepository;
	private MoveRepositoryInterface $moveRepository;
	private BreedingChainFinder $breedingChainFinder;
	private FormIconRepositoryInterface $formIconRepository;
	private GenerationRepositoryInterface $generationRepository;
	private PokemonNameRepositoryInterface $pokemonNameRepository;
	private MoveNameRepositoryInterface $moveNameRepository;
	private VersionGroupRepositoryInterface $versionGroupRepository;
	private PokemonEggGroupRepositoryInterface $pokemonEggGroupRepository;
	private EggGroupNameRepositoryInterface $eggGroupNameRepository;
	private SpeciesRepositoryInterface $speciesRepository;
	private PokemonMoveFormatter $pokemonMoveFormatter;


	private array $pokemon = [];
	private array $move = [];

	/** @var BreedingChainRecord[][] $chains */
	private array $chains = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param MoveRepositoryInterface $moveRepository
	 * @param BreedingChainFinder $breedingChainFinder
	 * @param FormIconRepositoryInterface $formIconRepository
	 * @param GenerationRepositoryInterface $generationRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param VersionGroupRepositoryInterface $versionGroupRepository
	 * @param PokemonEggGroupRepositoryInterface $pokemonEggGroupRepository
	 * @param EggGroupNameRepositoryInterface $eggGroupNameRepository
	 * @param SpeciesRepositoryInterface $speciesRepository
	 * @param PokemonMoveFormatter $pokemonMoveFormatter
	 */
	public function __construct(
		GenerationModel $generationModel,
		PokemonRepositoryInterface $pokemonRepository,
		MoveRepositoryInterface $moveRepository,
		BreedingChainFinder $breedingChainFinder,
		FormIconRepositoryInterface $formIconRepository,
		GenerationRepositoryInterface $generationRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		VersionGroupRepositoryInterface $versionGroupRepository,
		PokemonEggGroupRepositoryInterface $pokemonEggGroupRepository,
		EggGroupNameRepositoryInterface $eggGroupNameRepository,
		SpeciesRepositoryInterface $speciesRepository,
		PokemonMoveFormatter $pokemonMoveFormatter
	) {
		$this->generationModel = $generationModel;
		$this->pokemonRepository = $pokemonRepository;
		$this->moveRepository = $moveRepository;
		$this->breedingChainFinder = $breedingChainFinder;
		$this->formIconRepository = $formIconRepository;
		$this->generationRepository = $generationRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->moveNameRepository = $moveNameRepository;
		$this->versionGroupRepository = $versionGroupRepository;
		$this->pokemonEggGroupRepository = $pokemonEggGroupRepository;
		$this->eggGroupNameRepository = $eggGroupNameRepository;
		$this->speciesRepository = $speciesRepository;
		$this->pokemonMoveFormatter = $pokemonMoveFormatter;
	}

	/**
	 * Set breeding chain data for this Pokémon, move, and version group combination.
	 *
	 * @param string $generationIdentifier
	 * @param string $pokemonIdentifier
	 * @param string $moveIdentifier
	 * @param string $versionGroupIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $generationIdentifier,
		string $pokemonIdentifier,
		string $moveIdentifier,
		string $versionGroupIdentifier,
		LanguageId $languageId
	) : void {
		$generationId = $this->generationModel->setByIdentifier($generationIdentifier);

		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		$move = $this->moveRepository->getByIdentifier($moveIdentifier);
		$versionGroup = $this->versionGroupRepository->getByIdentifier(
			$versionGroupIdentifier
		);

		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemon->getId()
		);
		$this->pokemon = [
			'identifier' => $pokemon->getIdentifier(),
			'name' => $pokemonName->getName(),
		];

		$moveName = $this->moveNameRepository->getByLanguageAndMove($languageId, $move->getId());
		$this->move = [
			'name' => $moveName->getName(),
		];

		/** @var PokemonMove[][] $chains */
		$chains = $this->breedingChainFinder->findChains(
			$pokemon->getId(),
			$move->getId(),
			$versionGroup->getId()
		);

		$this->chains = [];
		foreach ($chains as $chain) {
			$chainId = [];
			$records = [];
			foreach ($chain as $pokemonMove) {
				$chainId[] = $pokemonMove->getPokemonId()->value();
				$records[] = $this->getRecord($generationId, $pokemonMove, $languageId);
			}
			$chainId = implode('-', $chainId);
			$this->chains[$chainId] = $records;
		}
	}

	/**
	 * Create the breeding chain record for this Pokémon move.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonMove $pokemonMove
	 * @param LanguageId $languageId
	 *
	 * @return BreedingChainRecord
	 */
	private function getRecord(
		GenerationId $generationId,
		PokemonMove $pokemonMove,
		LanguageId $languageId
	) : BreedingChainRecord {
		$pokemonId = $pokemonMove->getPokemonId();

		$formIcon = $this->formIconRepository->getByGenerationAndFormAndFemaleAndRight(
			$generationId,
			new FormId($pokemonId->value()),
			false,
			false
		);

		$versionGroup = $this->versionGroupRepository->getById(
			$pokemonMove->getVersionGroupId()
		);

		$generation = $this->generationRepository->getById(
			$versionGroup->getGenerationId()
		);

		$pokemon = $this->pokemonRepository->getById($pokemonId);

		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId
		);


		$eggGroupNames = [];
		$pokemonEggGroups = $this->pokemonEggGroupRepository->getByPokemon(
			$generationId,
			$pokemonId
		);
		foreach ($pokemonEggGroups as $pokemonEggGroup) {
			$eggGroupName = $this->eggGroupNameRepository->getByLanguageAndEggGroup(
				$languageId,
				$pokemonEggGroup->getEggGroupId()
			);
			$eggGroupNames[] = $eggGroupName->getName();
		}

		$species = $this->speciesRepository->getById($pokemon->getSpeciesId());

		return new BreedingChainRecord(
			$formIcon->getImage(),
			$generation->getIdentifier(),
			$pokemon->getIdentifier(),
			$pokemonName->getName(),
			$versionGroup->getIcon(),
			$eggGroupNames,
			$species->getBaseEggCycles(),
			$pokemon->getGenderRatio() . '.png',
			$this->pokemonMoveFormatter->format($pokemonMove, $languageId)
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
	 * Get the Pokémon.
	 *
	 * @return array
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
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
	 * Get the chains.
	 *
	 * @return BreedingChainRecord[][]
	 */
	public function getChains() : array
	{
		return $this->chains;
	}
}

