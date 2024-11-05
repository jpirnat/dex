<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Categories\DexCategoryRepositoryInterface;
use Jp\Dex\Domain\Items\ItemDescriptionRepositoryInterface;
use Jp\Dex\Domain\Items\TechnicalMachine;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodNameRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class DexPokemonMovesModel
{
	/** @var DexCategory[] $categories */
	private array $categories = [];

	/** @var DexVersionGroup[] $learnsetVgs */
	private array $learnsetVgs = [];

	/** @var DexPokemonMoveMethod[] $methods */
	private array $methods = [];


	public function __construct(
		private readonly DexCategoryRepositoryInterface $dexCategoryRepository,
		private readonly DexVersionGroupRepositoryInterface $dexVgRepository,
		private readonly PokemonMoveRepositoryInterface $pokemonMoveRepository,
		private readonly TmRepositoryInterface $tmRepository,
		private readonly ItemDescriptionRepositoryInterface $itemDescriptionRepository,
		private readonly DexMoveRepositoryInterface $dexMoveRepository,
		private readonly MoveMethodRepositoryInterface $moveMethodRepository,
		private readonly MoveMethodNameRepositoryInterface $moveMethodNameRepository,
	) {}


	/**
	 * Set data for the dex Pokémon page's moves table.
	 */
	public function setData(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : void {
		$this->categories = [];
		$this->learnsetVgs = [];
		$this->methods = [];

		$this->categories = $this->dexCategoryRepository->getByLanguage(
			$languageId,
		);

		$this->learnsetVgs = $this->dexVgRepository->getByIntoVgWithPokemon(
			$versionGroupId,
			$pokemonId,
			$languageId,
		);

		$pokemonMoves = $this->pokemonMoveRepository->getByIntoVgAndPokemon(
			$versionGroupId,
			$pokemonId,
		);

		// Get all the TMs that could show up in the table.
		$tms = $this->tmRepository->getByIntoVg(
			$versionGroupId,
		);
		$itemDescriptions = $this->itemDescriptionRepository->getTmsByIntoVg(
			$versionGroupId,
			$languageId,
		);

		$levelUpMoves = [];
		$moveVgIndexes = [];
		$methodsMoves = [];
		foreach ($pokemonMoves as $pokemonMove) {
			$vgId = $pokemonMove->getVersionGroupId()->value();
			$moveId = $pokemonMove->getMoveId()->value();
			$methodId = $pokemonMove->getMoveMethodId()->value();

			if (!isset($this->learnsetVgs[$vgId])) {
				// This should only happen if this Pokémon move is from a gen 1
				// version group of the wrong locality (Red/Green vs Red/Blue).
				continue;
			}
			$vgIdentifier = $this->learnsetVgs[$vgId]->getIdentifier();

			switch ($methodId) {
				case MoveMethodId::LEVEL_UP:
					// Across version groups, a Pokémon could learn a move at
					// different levels, or multiple times. So, we need to track
					// how many times the move is learned in each version group,
					// to know how many dex Pokémon move records a level up move
					// will need.
					if (isset($moveVgIndexes[$moveId][$vgIdentifier])) {
						$moveVgIndexes[$moveId][$vgIdentifier]++;
					} else {
						$moveVgIndexes[$moveId][$vgIdentifier] = 0;
					}

					$index = $moveVgIndexes[$moveId][$vgIdentifier];
					$level = $pokemonMove->getLevel();
					$levelUpMoves[$moveId][$index][$vgIdentifier] = $level;
					break;
				case MoveMethodId::MACHINE:
					// The version group data is the TM's number.
					/** @var TechnicalMachine $tm */
					$tm = $tms[$vgId][$moveId];
					$itemId = $tm->getItemId()->value();
					$itemDescription = $itemDescriptions[$vgId][$itemId];

					$methodsMoves[$methodId][$moveId][$vgIdentifier] = [
						'machineType' => $tm->getMachineType()->value(),
						'number' => $tm->getNumber(),
						'item' => $itemDescription->getName(),
					];
					break;
				default:
					// The version group data is just that the Pokémon learns
					// the move in this version group.
					$methodsMoves[$methodId][$moveId][$vgIdentifier] = 1;
					break;
			}
		}

		// Get move data.
		$moves = $this->dexMoveRepository->getByPokemon(
			$versionGroupId,
			$pokemonId,
			$languageId,
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
					$move->getDescription(),
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
					$move->getDescription(),
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
				$dexPokemonMoves[$methodId],
			);
		}
	}


	/**
	 * @return DexCategory[]
	 */
	public function getCategories() : array
	{
		return $this->categories;
	}

	/**
	 * @return DexVersionGroup[]
	 */
	public function getLearnsetVgs() : array
	{
		return $this->learnsetVgs;
	}

	/**
	 * @return DexPokemonMoveMethod[]
	 */
	public function getMethods() : array
	{
		return $this->methods;
	}
}
