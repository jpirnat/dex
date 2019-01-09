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
use Jp\Dex\Domain\Moves\Move;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\Pokemon;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\PokemonMove;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveFormatter;
use Jp\Dex\Domain\Species\SpeciesRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

class BreedingChainsModel
{
	/** @var GenerationModel $generationModel */
	private $generationModel;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var MoveRepositoryInterface $moveRepository */
	private $moveRepository;

	/** @var BreedingChainFinder $breedingChainFinder */
	private $breedingChainFinder;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;

	/** @var GenerationRepositoryInterface $generationRepository */
	private $generationRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var VersionGroupRepositoryInterface $versionGroupRepository */
	private $versionGroupRepository;

	/** @var PokemonEggGroupRepositoryInterface $pokemonEggGroupRepository */
	private $pokemonEggGroupRepository;

	/** @var EggGroupNameRepositoryInterface $eggGroupNameRepository */
	private $eggGroupNameRepository;

	/** @var SpeciesRepositoryInterface $speciesRepository */
	private $speciesRepository;

	/** @var PokemonMoveFormatter $pokemonMoveFormatter */
	private $pokemonMoveFormatter;


	/** @var Pokemon $pokemon */
	private $pokemon;

	/** @var Move $move */
	private $move;

	/** @var VersionGroup $versionGroup */
	private $versionGroup;

	/** @var BreedingChainRecord[][] $chains */
	private $chains = [];


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
		$this->versionGroupRepository = $versionGroupRepository;
		$this->pokemonEggGroupRepository = $pokemonEggGroupRepository;
		$this->eggGroupNameRepository = $eggGroupNameRepository;
		$this->speciesRepository = $speciesRepository;
		$this->pokemonMoveFormatter = $pokemonMoveFormatter;
	}

	/**
	 * Set breeding chain data for this Pokémon, move, and version group combination.
	 *
	 * @param string $pokemonIdentifier
	 * @param string $moveIdentifier
	 * @param string $versionGroupIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $pokemonIdentifier,
		string $moveIdentifier,
		string $versionGroupIdentifier,
		LanguageId $languageId
	) : void {
		$this->pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		$this->move = $this->moveRepository->getByIdentifier($moveIdentifier);
		$this->versionGroup = $this->versionGroupRepository->getByIdentifier(
			$versionGroupIdentifier
		);

		$generationId = $this->generationModel->setById(
			$this->versionGroup->getGenerationId()
		);

		$chains = $this->breedingChainFinder->findChains(
			$this->pokemon->getId(),
			$this->move->getId(),
			$this->versionGroup->getId()
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
	 * Get the chains.
	 *
	 * @return BreedingChainRecord[][]
	 */
	public function getChains() : array
	{
		return $this->chains;
	}
}

