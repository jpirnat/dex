<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Application\Models\StatNameModel;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodNameRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexMovePokemonModel
{
	private array $stats = [];

	/** @var DexMovePokemonMethod[] $methods */
	private array $methods = [];


	public function __construct(
		private PokemonMoveRepositoryInterface $pokemonMoveRepository,
		private TmRepositoryInterface $tmRepository,
		private ItemNameRepositoryInterface $itemNameRepository,
		private DexPokemonRepositoryInterface $dexPokemonRepository,
		private MoveMethodRepositoryInterface $moveMethodRepository,
		private MoveMethodNameRepositoryInterface $moveMethodNameRepository,
		private StatNameModel $statNameModel,
	) {}


	/**
	 * Set data for the dex move page's Pokémon table.
	 *
	 * @param MoveId $moveId
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param DexVersionGroup[] $versionGroups
	 *
	 * @return void
	 */
	public function setData(
		MoveId $moveId,
		GenerationId $generationId,
		LanguageId $languageId,
		array $versionGroups
	) : void {
		$pokemonMoves = $this->pokemonMoveRepository->getByMoveAndGeneration(
			$moveId,
			$generationId
		);

		// Get all the TMs that could show up in the table.
		if (MoveId::TYPED_HIDDEN_POWER_BEGIN <= $moveId->value()
			&& $moveId->value() <= MoveId::TYPED_HIDDEN_POWER_END
		) {
			// The TMs table does not track typed Hidden Powers, so look up the
			// TMs for the real move instead.
			$hiddenPower = new MoveId(MoveId::HIDDEN_POWER);
			$tms = $this->tmRepository->getByMove($hiddenPower);
		} else {
			$tms = $this->tmRepository->getByMove($moveId);
		}

		$pokemonIds = [];
		$methodsPokemons = [];
		foreach ($pokemonMoves as $pokemonMove) {
			$pokemonId = $pokemonMove->getPokemonId()->value();
			$vgId = $pokemonMove->getVersionGroupId()->value();
			$methodId = $pokemonMove->getMoveMethodId()->value();

			if (!isset($versionGroups[$vgId])) {
				// This should only happen if this Pokémon move is from a gen 1
				// version group of the wrong locality (Red/Green vs Red/Blue).
				continue;
			}
			$vgIdentifier = $versionGroups[$vgId]->getIdentifier();

			// Keep track of the Pokémon we'll need data for.
			$pokemonIds[$pokemonId] = $pokemonMove->getPokemonId();

			switch ($methodId) {
				case MoveMethodId::LEVEL_UP:
					// The version group data is the lowest level at which the
					// Pokémon learns the move.
					$level = $pokemonMove->getLevel();
					$oldLevel = $methodsPokemons[$methodId][$pokemonId][$vgIdentifier] ?? 101;
					if ($level <  $oldLevel) {
						$methodsPokemons[$methodId][$pokemonId][$vgIdentifier] = $level;
					}
					break;
				case MoveMethodId::MACHINE:
					// The version group data is the TM's number.
					$tm = $tms[$vgId];
					$itemName = $this->itemNameRepository->getByLanguageAndItem(
						$languageId,
						$tm->getItemId()
					);
					$methodsPokemons[$methodId][$pokemonId][$vgIdentifier] = $itemName->getName();
					break;
				default:
					// The version group data is just that the Pokémon learns
					// the move in this version group.
					$methodsPokemons[$methodId][$pokemonId][$vgIdentifier] = 1;
					break;
			}
		}

		// Get Pokémon data.
		$pokemons = $this->dexPokemonRepository->getWithMove(
			$generationId,
			$moveId,
			$languageId
		);

		// Compile the dex move Pokémon records.
		$dexMovePokemon = [];
		foreach ($methodsPokemons as $methodId => $methodPokemons) {
			foreach ($methodPokemons as $pokemonId => $versionGroupData) {
				if (!isset($pokemons[$pokemonId])) {
					// This Pokémon that used to exist in an older generation
					// does not exist in the current generation!
					continue;
				}

				$pokemon = $pokemons[$pokemonId];

				$dexMovePokemon[$methodId][] = new DexMovePokemon(
					$versionGroupData,
					$pokemon->getIcon(),
					$pokemon->getIdentifier(),
					$pokemon->getName(),
					$pokemon->getTypes(),
					$pokemon->getAbilities(),
					$pokemon->getBaseStats(),
					$pokemon->getBst(),
					$pokemon->getSort()
				);
			}
		}

		// Get other data for the dex move Pokémon method records.
		$moveMethods = $this->moveMethodRepository->getAll();
		$moveMethodNames = $this->moveMethodNameRepository->getByLanguage(
			$languageId
		);

		// Get stat name abbreviations.
		$this->stats = $this->statNameModel->getByGeneration($generationId, $languageId);

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
	 * Get the stats and their names.
	 *
	 * @return array
	 */
	public function getStats() : array
	{
		return $this->stats;
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
