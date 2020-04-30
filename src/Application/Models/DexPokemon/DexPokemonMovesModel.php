<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Items\TechnicalMachine;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodNameRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexPokemonMovesModel
{
	private PokemonMoveRepositoryInterface $pokemonMoveRepository;
	private TmRepositoryInterface $tmRepository;
	private ItemNameRepositoryInterface $itemNameRepository;
	private DexMoveRepositoryInterface $dexMoveRepository;
	private MoveMethodRepositoryInterface $moveMethodRepository;
	private MoveMethodNameRepositoryInterface $moveMethodNameRepository;


	/** @var DexPokemonMoveMethod[] $methods */
	private array $methods = [];


	/**
	 * Constructor.
	 *
	 * @param PokemonMoveRepositoryInterface $pokemonMoveRepository
	 * @param TmRepositoryInterface $tmRepository
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 * @param DexMoveRepositoryInterface $dexMoveRepository
	 * @param MoveMethodRepositoryInterface $moveMethodRepository
	 * @param MoveMethodNameRepositoryInterface $moveMethodNameRepository
	 */
	public function __construct(
		PokemonMoveRepositoryInterface $pokemonMoveRepository,
		TmRepositoryInterface $tmRepository,
		ItemNameRepositoryInterface $itemNameRepository,
		DexMoveRepositoryInterface $dexMoveRepository,
		MoveMethodRepositoryInterface $moveMethodRepository,
		MoveMethodNameRepositoryInterface $moveMethodNameRepository
	) {
		$this->pokemonMoveRepository = $pokemonMoveRepository;
		$this->tmRepository = $tmRepository;
		$this->itemNameRepository = $itemNameRepository;
		$this->dexMoveRepository = $dexMoveRepository;
		$this->moveMethodRepository = $moveMethodRepository;
		$this->moveMethodNameRepository = $moveMethodNameRepository;
	}


	/**
	 * Set data for the dex Pokémon page's moves table.
	 *
	 * @param PokemonId $pokemonId
	 * @param GenerationId $introducedInGenerationId
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		PokemonId $pokemonId,
		GenerationId $introducedInGenerationId,
		GenerationId $generationId,
		LanguageId $languageId
	) : void {
		$pokemonMoves = $this->pokemonMoveRepository->getByPokemonAndGeneration(
			$pokemonId,
			$generationId
		);

		// Get all the TMs that could show up in the table.
		$tms = $this->tmRepository->getBetween(
			$introducedInGenerationId,
			$generationId
		);

		$moveIds = [];
		$levelUpMoves = [];
		$moveVgIndexes = [];
		$methodsMoves = [];
		foreach ($pokemonMoves as $pokemonMove) {
			$vgId = $pokemonMove->getVersionGroupId()->value();
			$moveId = $pokemonMove->getMoveId()->value();
			$methodId = $pokemonMove->getMoveMethodId()->value();

			// Keep track of moves we'll need data for.
			$moveIds[$moveId] = $pokemonMove->getMoveId();

			switch ($methodId) {
				case MoveMethodId::LEVEL_UP:
					// Across version groups, a Pokémon could learn a move at
					// different levels, or multiple times. So, we need to track
					// how many times the move is learned in each version group,
					// to know how many dex Pokémon move records a level up move
					// will need.
					if (isset($moveVgIndexes[$moveId][$vgId])) {
						$moveVgIndexes[$moveId][$vgId]++;
					} else {
						$moveVgIndexes[$moveId][$vgId] = 0;
					}

					$index = $moveVgIndexes[$moveId][$vgId];
					$level = $pokemonMove->getLevel();
					$levelUpMoves[$moveId][$index][$vgId] = $level;
					break;
				case MoveMethodId::MACHINE:
					// The version group data is the TM's number.
					/** @var TechnicalMachine $tm */
					$tm = $tms[$vgId][$moveId];
					$itemName = $this->itemNameRepository->getByLanguageAndItem(
						$languageId,
						$tm->getItemId()
					);
					$methodsMoves[$methodId][$moveId][$vgId] = $itemName->getName();
					break;
				default:
					// The version group data is just that the Pokémon learns
					// the move in this version group.
					$methodsMoves[$methodId][$moveId][$vgId] = 1;
					break;
			}
		}

		// Get move data.
		$moves = $this->dexMoveRepository->getByPokemon(
			$generationId,
			$pokemonId,
			$languageId
		);

		// Compile the dex Pokémon move records.
		$dexPokemonMoves = [];
		foreach ($levelUpMoves as $moveId => $indexedMoves) {
			if (!isset($moves[$moveId])) {
				// This move that the Pokémon could learn in an older generation
				// does not exist in the current generation!
				continue;
			}

			$move = $moves[$moveId];

			foreach ($indexedMoves as $versionGroupData) {
				$dexPokemonMoves[MoveMethodId::LEVEL_UP][] = new DexPokemonMove(
					$versionGroupData,
					$move->getIdentifier(),
					$move->getName(),
					$move->getType(),
					$move->getCategory(),
					$move->getPP(),
					$move->getPower(),
					$move->getAccuracy(),
					$move->getDescription()
				);
			}
		}
		foreach ($methodsMoves as $methodId => $methodMoves) {
			foreach ($methodMoves as $moveId => $versionGroupData) {
				if (!isset($moves[$moveId])) {
					// This move that the Pokémon could learn in an older
					// generation does not exist in the current generation!
					continue;
				}

				$move = $moves[$moveId];

				$dexPokemonMoves[$methodId][] = new DexPokemonMove(
					$versionGroupData,
					$move->getIdentifier(),
					$move->getName(),
					$move->getType(),
					$move->getCategory(),
					$move->getPP(),
					$move->getPower(),
					$move->getAccuracy(),
					$move->getDescription()
				);
			}
		}

		// Get other data for the dex Pokémon move method records.
		$moveMethods = $this->moveMethodRepository->getAll();
		$moveMethodNames = $this->moveMethodNameRepository->getByLanguage(
			$languageId
		);

		// Compile the dex Pokémon move method records.
		foreach ($moveMethods as $methodId => $moveMethod) {
			if (!isset($dexPokemonMoves[$methodId])) {
				continue; // This Pokémon learns no moves via this move method.
			}

			$this->methods[$methodId] = new DexPokemonMoveMethod(
				$moveMethod->getIdentifier(),
				$moveMethodNames[$methodId]->getName(),
				$moveMethodNames[$methodId]->getDescription(),
				$dexPokemonMoves[$methodId]
			);
		}
	}

	/**
	 * Get the move methods.
	 *
	 * @return DexPokemonMoveMethod[]
	 */
	public function getMethods() : array
	{
		return $this->methods;
	}
}
