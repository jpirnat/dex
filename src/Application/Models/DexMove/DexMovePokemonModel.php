<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Application\Models\Structs\DexPokemonAbilityFactory;
use Jp\Dex\Application\Models\Structs\DexTypeFactory;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodNameRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

class DexMovePokemonModel
{
	/** @var PokemonMoveRepositoryInterface $pokemonMoveRepository */
	private $pokemonMoveRepository;

	/** @var TmRepositoryInterface $tmRepository */
	private $tmRepository;

	/** @var DexTypeFactory $dexTypeFactory */
	private $dexTypeFactory;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var PokemonTypeRepositoryInterface $pokemonTypeRepository */
	private $pokemonTypeRepository;

	/** @var DexPokemonAbilityFactory $dexPokemonAbilityFactory */
	private $dexPokemonAbilityFactory;

	/** @var BaseStatRepositoryInterface $baseStatRepository */
	private $baseStatRepository;

	/** @var MoveMethodRepositoryInterface $moveMethodRepository */
	private $moveMethodRepository;

	/** @var MoveMethodNameRepositoryInterface $moveMethodNameRepository */
	private $moveMethodNameRepository;

	/** @var StatNameRepositoryInterface $statNameRepository */
	private $statNameRepository;


	/** @var string[] $statAbbreviations */
	private $statAbbreviations = [];

	/** @var DexMovePokemonMethod[] $methods */
	private $methods = [];


	/**
	 * Constructor.
	 *
	 * @param PokemonMoveRepositoryInterface $pokemonMoveRepository
	 * @param TmRepositoryInterface $tmRepository
	 * @param DexTypeFactory $dexTypeFactory
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param PokemonTypeRepositoryInterface $pokemonTypeRepository
	 * @param DexPokemonAbilityFactory $dexPokemonAbilityFactory
	 * @param BaseStatRepositoryInterface $baseStatRepository
	 * @param MoveMethodRepositoryInterface $moveMethodRepository
	 * @param MoveMethodNameRepositoryInterface $moveMethodNameRepository
	 * @param StatNameRepositoryInterface $statNameRepository
	 */
	public function __construct(
		PokemonMoveRepositoryInterface $pokemonMoveRepository,
		TmRepositoryInterface $tmRepository,
		DexTypeFactory $dexTypeFactory,
		PokemonRepositoryInterface $pokemonRepository,
		FormIconRepositoryInterface $formIconRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		PokemonTypeRepositoryInterface $pokemonTypeRepository,
		DexPokemonAbilityFactory $dexPokemonAbilityFactory,
		BaseStatRepositoryInterface $baseStatRepository,
		MoveMethodRepositoryInterface $moveMethodRepository,
		MoveMethodNameRepositoryInterface $moveMethodNameRepository,
		StatNameRepositoryInterface $statNameRepository
	) {
		$this->pokemonMoveRepository = $pokemonMoveRepository;
		$this->tmRepository = $tmRepository;
		$this->dexTypeFactory = $dexTypeFactory;
		$this->pokemonRepository = $pokemonRepository;
		$this->formIconRepository = $formIconRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->pokemonTypeRepository = $pokemonTypeRepository;
		$this->dexPokemonAbilityFactory = $dexPokemonAbilityFactory;
		$this->baseStatRepository = $baseStatRepository;
		$this->moveMethodRepository = $moveMethodRepository;
		$this->moveMethodNameRepository = $moveMethodNameRepository;
		$this->statNameRepository = $statNameRepository;
	}


	/**
	 * Set data for the dex move page's Pokémon table.
	 *
	 * @param MoveId $moveId
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		MoveId $moveId,
		GenerationId $generationId,
		LanguageId $languageId
	) : void {
		$pokemonMoves = $this->pokemonMoveRepository->getByMoveAndGeneration(
			$moveId,
			$generationId
		);

		// Get all the TMs that could show up in the table.
		$tms = $this->tmRepository->getByMove($moveId);

		$pokemonIds = [];
		$methodsPokemons = [];
		foreach ($pokemonMoves as $pokemonMove) {
			$pokemonId = $pokemonMove->getPokemonId()->value();
			$vgId = $pokemonMove->getVersionGroupId()->value();
			$methodId = $pokemonMove->getMoveMethodId()->value();

			// Keep track of the Pokémon we'll need data for.
			$pokemonIds[$pokemonId] = $pokemonMove->getPokemonId();

			switch ($methodId) {
				case MoveMethodId::LEVEL_UP:
					// The version group data is the lowest level at which the
					// Pokémon learns the move.
					$level = $pokemonMove->getLevel();
					$oldLevel = $methodsPokemons[$methodId][$pokemonId][$vgId] ?? 101;
					if ($level <  $oldLevel) {
						$methodsPokemons[$methodId][$pokemonId][$vgId] = $level;
					}
					break;
				case MoveMethodId::MACHINE:
					// The version group data is the TM's number.
					$tm = $tms[$vgId];
					$number = $tm->isHm()
						? 'H' . $tm->getNumber()
						: str_pad((string) $tm->getNumber(), 2, '0');
					$methodsPokemons[$methodId][$pokemonId][$vgId] = $number;
					break;
				default:
					// The version group data is just that the Pokémon learns
					// the move in this version group.
					$methodsPokemons[$methodId][$pokemonId][$vgId] = 1;
					break;
			}
		}

		// Get miscellaneous data to help create the dex move Pokémon records.
		$dexTypes = $this->dexTypeFactory->getAll($generationId, $languageId);
		$statIds = StatId::getByGeneration($generationId);

		// Get Pokémon data.
		$pokemon = [];
		$pokemonIcons = [];
		$pokemonNames = [];
		$pokemonsTypes = [];
		$pokemonAbilities = [];
		$pokemonBaseStats = [];
		$pokemonBaseStatTotals = [];
		foreach ($pokemonIds as $id => $pokemonId) {
			$pokemonTypes = $this->pokemonTypeRepository->getByGenerationAndPokemon(
				$generationId,
				$pokemonId
			);
			if ($pokemonTypes === []) {
				// This Pokémon that used to exist in an older generation does
				// not exist in the current generation! (It's probably Spiky-eared
				// Pichu or ???-type Arceus.)
				continue;
			}

			$pokemon[$id] = $this->pokemonRepository->getById($pokemonId);
			$pokemonIcons[$id] = $this->formIconRepository->getByGenerationAndFormAndFemaleAndRight(
				$generationId,
				new FormId($id),
				false,
				false
			);
			$pokemonNames[$id] = $this->pokemonNameRepository->getByLanguageAndPokemon(
				$languageId,
				$pokemonId
			);

			// Get the Pokémon's dex types.
			foreach ($pokemonTypes as $pokemonType) {
				$typeId = $pokemonType->getTypeId()->value();
				$pokemonsTypes[$id][] = $dexTypes[$typeId];
			}

			// Get the Pokémon's dex abilities.
			$pokemonAbilities[$id] = $this->dexPokemonAbilityFactory->getByPokemon(
				$generationId,
				$pokemonId,
				$languageId
			);

			// Get the Pokémon's base stats.
			$baseStats = $this->baseStatRepository->getByGenerationAndPokemon(
				$generationId,
				$pokemonId
			);
			foreach ($statIds as $statId) {
				$pokemonBaseStats[$id][] = $baseStats->get($statId)->getValue();
			}

			// Get the Pokémon's base stat total.
			$pokemonBaseStatTotals[$id] = (int) array_sum($pokemonBaseStats[$id]);
		}

		// Compile the dex move Pokémon records.
		$dexMovePokemon = [];
		foreach ($methodsPokemons as $methodId => $methodPokemons) {
			foreach ($methodPokemons as $pokemonId => $versionGroupData) {
				if (!isset($pokemon[$pokemonId])) {
					// This Pokémon that used to exist in an older generation
					// does not exist in the current generation!
					continue;
				}

				$dexMovePokemon[$methodId][] = new DexMovePokemon(
					$versionGroupData,
					$pokemonIcons[$pokemonId]->getImage(),
					$pokemon[$pokemonId]->getIdentifier(),
					$pokemonNames[$pokemonId]->getName(),
					$pokemonsTypes[$pokemonId],
					$pokemonAbilities[$pokemonId],
					$pokemonBaseStats[$pokemonId],
					$pokemonBaseStatTotals[$pokemonId],
					$pokemon[$pokemonId]->getSort()
				);
			}
		}

		// Get other data for the dex move Pokémon method records.
		$moveMethods = $this->moveMethodRepository->getAll();
		$moveMethodNames = $this->moveMethodNameRepository->getByLanguage(
			$languageId
		);

		// Get stat name abbreviations.
		$statNames = $this->statNameRepository->getByLanguage($languageId);
		$this->statAbbreviations = [];
		foreach ($statIds as $statId) {
			$this->statAbbreviations[] = $statNames[$statId->value()]->getAbbreviation();
		}

		// Compile the dex move Pokémon method records.
		foreach ($moveMethods as $methodId => $moveMethod) {
			if (!isset($dexMovePokemon[$methodId])) {
				// No Pokémon learns this move via this move method.
				continue;
			}

			$this->methods[$methodId] = new DexMovePokemonMethod(
				$moveMethod->getIdentifier(),
				$moveMethodNames[$methodId]->getName(),
				$moveMethodNames[$methodId]->getDescription(),
				$dexMovePokemon[$methodId]
			);
		}
	}


	/**
	 * Get the stat abbreviations.
	 *
	 * @return string[]
	 */
	public function getStatAbbreviations() : array
	{
		return $this->statAbbreviations;
	}

	/**
	 * Get the move methods.
	 *
	 * @return DexMovePokemonMethod[]
	 */
	public function getMethods() : array
	{
		return $this->methods;
	}
}
