<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Application\Models\Structs\DexTypeFactory;
use Jp\Dex\Domain\Categories\CategoryRepositoryInterface;
use Jp\Dex\Domain\Items\TechnicalMachine;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\GenerationMoveNotFoundException;
use Jp\Dex\Domain\Moves\GenerationMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveDescriptionRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodNameRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

class DexPokemonMovesModel
{
	/** @var PokemonMoveRepositoryInterface $pokemonMoveRepository */
	private $pokemonMoveRepository;

	/** @var TmRepositoryInterface $tmRepository */
	private $tmRepository;

	/** @var DexTypeFactory $dexTypeFactory */
	private $dexTypeFactory;

	/** @var CategoryRepositoryInterface $categoryRepository */
	private $categoryRepository;

	/** @var GenerationMoveRepositoryInterface $generationMoveRepository */
	private $generationMoveRepository;

	/** @var MoveRepositoryInterface $moveRepository */
	private $moveRepository;

	/** @var MoveNameRepositoryInterface $moveNameRepository */
	private $moveNameRepository;

	/** @var MoveDescriptionRepositoryInterface $moveDescriptionRepository */
	private $moveDescriptionRepository;

	/** @var MoveMethodRepositoryInterface $moveMethodRepository */
	private $moveMethodRepository;

	/** @var MoveMethodNameRepositoryInterface $moveMethodNameRepository */
	private $moveMethodNameRepository;


	/** @var DexPokemonMoveMethod[] $methods */
	private $methods = [];


	/**
	 * Constructor.
	 *
	 * @param PokemonMoveRepositoryInterface $pokemonMoveRepository
	 * @param TmRepositoryInterface $tmRepository
	 * @param DexTypeFactory $dexTypeFactory
	 * @param CategoryRepositoryInterface $categoryRepository
	 * @param GenerationMoveRepositoryInterface $generationMoveRepository
	 * @param MoveRepositoryInterface $moveRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param MoveDescriptionRepositoryInterface $moveDescriptionRepository
	 * @param MoveMethodRepositoryInterface $moveMethodRepository
	 * @param MoveMethodNameRepositoryInterface $moveMethodNameRepository
	 */
	public function __construct(
		PokemonMoveRepositoryInterface $pokemonMoveRepository,
		TmRepositoryInterface $tmRepository,
		DexTypeFactory $dexTypeFactory,
		CategoryRepositoryInterface $categoryRepository,
		GenerationMoveRepositoryInterface $generationMoveRepository,
		MoveRepositoryInterface $moveRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		MoveDescriptionRepositoryInterface $moveDescriptionRepository,
		MoveMethodRepositoryInterface $moveMethodRepository,
		MoveMethodNameRepositoryInterface $moveMethodNameRepository
	) {
		$this->pokemonMoveRepository = $pokemonMoveRepository;
		$this->tmRepository = $tmRepository;
		$this->dexTypeFactory = $dexTypeFactory;
		$this->categoryRepository = $categoryRepository;
		$this->generationMoveRepository = $generationMoveRepository;
		$this->moveRepository = $moveRepository;
		$this->moveNameRepository = $moveNameRepository;
		$this->moveDescriptionRepository = $moveDescriptionRepository;
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
					$number = $tm->isHm()
						? 'H' . $tm->getNumber()
						: str_pad((string) $tm->getNumber(), 2, '0', STR_PAD_LEFT);
					$methodsMoves[$methodId][$moveId][$vgId] = $number;
					break;
				default:
					// The version group data is just that the Pokémon learns
					// the move in this version group.
					$methodsMoves[$methodId][$moveId][$vgId] = 1;
					break;
			}
		}

		// Get miscellaneous data to help create the dex Pokémon move records.
		$dexTypes = $this->dexTypeFactory->getAll($generationId, $languageId);
		$categories = $this->categoryRepository->getAll();

		// Get move data.
		$moves = [];
		$moveNames = [];
		$generationMoves = [];
		$moveDescriptions = [];
		foreach ($moveIds as $id => $moveId) {
			try {
				$generationMove = $this->generationMoveRepository->getByGenerationAndMove(
					$generationId,
					$moveId
				);
			} catch (GenerationMoveNotFoundException $e) {
				// This move that the Pokémon could learn in an older generation
				// does not exist in the current generation! (It's probably a
				// Shadow move.)
				continue;
			}
			$moves[$id] = $this->moveRepository->getById($moveId);
			$moveNames[$id] = $this->moveNameRepository->getByLanguageAndMove(
				$languageId,
				$moveId
			);
			$generationMoves[$id] = $generationMove;
			$moveDescriptions[$id] = $this->moveDescriptionRepository->getByGenerationAndLanguageAndMove(
				$generationId,
				$languageId,
				$moveId
			);
		}

		// Compile the dex Pokémon move records.
		$dexPokemonMoves = [];
		foreach ($levelUpMoves as $moveId => $indexedMoves) {
			if (!isset($generationMoves[$moveId])) {
				// This move that the Pokémon could learn in an older generation
				// does not exist in the current generation!
				continue;
			}

			$generationMove = $generationMoves[$moveId];
			$typeId = $generationMove->getTypeId()->value();
			$categoryId = $generationMove->getCategoryId()->value();
			foreach ($indexedMoves as $versionGroupData) {
				$dexPokemonMoves[MoveMethodId::LEVEL_UP][] = new DexPokemonMove(
					$versionGroupData,
					$moves[$moveId]->getIdentifier(),
					$moveNames[$moveId]->getName(),
					$dexTypes[$typeId],
					$categories[$categoryId]->getIcon(),
					$generationMove->getPP(),
					$generationMove->getPower(),
					$generationMove->getAccuracy(),
					$moveDescriptions[$moveId]->getDescription()
				);
			}
		}
		foreach ($methodsMoves as $methodId => $methodMoves) {
			foreach ($methodMoves as $moveId => $versionGroupData) {
				if (!isset($generationMoves[$moveId])) {
					// This move that the Pokémon could learn in an older
					// generation does not exist in the current generation!
					continue;
				}

				$generationMove = $generationMoves[$moveId];
				$typeId = $generationMove->getTypeId()->value();
				$categoryId = $generationMove->getCategoryId()->value();

				$dexPokemonMoves[$methodId][] = new DexPokemonMove(
					$versionGroupData,
					$moves[$moveId]->getIdentifier(),
					$moveNames[$moveId]->getName(),
					$dexTypes[$typeId],
					$categories[$categoryId]->getIcon(),
					$generationMove->getPP(),
					$generationMove->getPower(),
					$generationMove->getAccuracy(),
					$moveDescriptions[$moveId]->getDescription()
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
