<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Domain\Items\ItemDescriptionRepositoryInterface;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodNameRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class DexMovePokemonModel
{
	/** @var DexVersionGroup[] $learnsetVgs */
	private(set) array $learnsetVgs = [];

	private(set) array $stats = [];

	/** @var DexMovePokemonMethod[] $methods */
	private(set) array $methods = [];


	public function __construct(
		private readonly DexVersionGroupRepositoryInterface $dexVgRepository,
		private readonly PokemonMoveRepositoryInterface $pokemonMoveRepository,
		private readonly TmRepositoryInterface $tmRepository,
		private readonly ItemDescriptionRepositoryInterface $itemDescriptionRepository,
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
		private readonly MoveMethodRepositoryInterface $moveMethodRepository,
		private readonly MoveMethodNameRepositoryInterface $moveMethodNameRepository,
		private readonly DexStatRepositoryInterface $dexStatRepository,
	) {}


	/**
	 * Set data for the dex move page's Pokémon table.
	 */
	public function setData(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : void {
		$this->learnsetVgs = [];
		$this->stats = [];
		$this->methods = [];

		$this->learnsetVgs = $this->dexVgRepository->getByIntoVgWithMove(
			$versionGroupId,
			$moveId,
			$languageId,
		);

		$pokemonMoves = $this->pokemonMoveRepository->getByIntoVgAndMove(
			$versionGroupId,
			$moveId,
		);

		// Get data for the real Hidden Power instead of typed Hidden Powers.
		if (MoveId::TYPED_HIDDEN_POWER_BEGIN <= $moveId->value()
			&& $moveId->value() <= MoveId::TYPED_HIDDEN_POWER_END
		) {
			$moveId = new MoveId(MoveId::HIDDEN_POWER);
		}

		// Get all the TMs that could show up in the table.
		$tms = $this->tmRepository->getByMove($moveId);
		$itemDescriptions = $this->itemDescriptionRepository->getTmsByIntoVgAndMove(
			$versionGroupId,
			$moveId,
			$languageId,
		);

		$methodsPokemons = [];
		foreach ($pokemonMoves as $pokemonMove) {
			$pokemonId = $pokemonMove->pokemonId->value();
			$vgId = $pokemonMove->versionGroupId->value();
			$methodId = $pokemonMove->moveMethodId->value();

			if (!isset($this->learnsetVgs[$vgId])) {
				// This should only happen if this Pokémon move is from a gen 1
				// version group of the wrong locality (Red/Green vs Red/Blue).
				continue;
			}
			$vgIdentifier = $this->learnsetVgs[$vgId]->getIdentifier();

			switch ($methodId) {
				case MoveMethodId::LEVEL_UP:
					// The version group data is the lowest level at which the
					// Pokémon learns the move.
					$level = $pokemonMove->level;
					$oldLevel = $methodsPokemons[$methodId][$pokemonId][$vgIdentifier] ?? 101;
					if ($level <  $oldLevel) {
						$methodsPokemons[$methodId][$pokemonId][$vgIdentifier] = $level;
					}
					break;
				case MoveMethodId::MACHINE:
					// The version group data is the TM's number.
					$tm = $tms[$vgId];
					$itemId = $tm->itemId->value();
					$itemDescription = $itemDescriptions[$vgId][$itemId];

					$methodsPokemons[$methodId][$pokemonId][$vgIdentifier] = $itemDescription->name;
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
			$versionGroupId,
			$moveId,
			$languageId,
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
					$pokemon->icon,
					$pokemon->identifier,
					$pokemon->name,
					$pokemon->types,
					$pokemon->abilities,
					$pokemon->baseStats,
					$pokemon->bst,
					$pokemon->sort,
				);
			}
		}

		// Get other data for the dex move Pokémon method records.
		$moveMethods = $this->moveMethodRepository->getAll();
		$moveMethodNames = $this->moveMethodNameRepository->getByLanguage(
			$languageId
		);

		$this->stats = $this->dexStatRepository->getByVersionGroup($versionGroupId, $languageId);

		// Compile the dex move Pokémon method records.
		foreach ($moveMethods as $methodId => $moveMethod) {
			if (!isset($dexMovePokemon[$methodId])) {
				// No Pokémon learns this move via this move method.
				continue;
			}

			$this->methods[$methodId] = new DexMovePokemonMethod(
				$moveMethod->identifier,
				$moveMethodNames[$methodId]->name,
				$moveMethodNames[$methodId]->description,
				$dexMovePokemon[$methodId],
			);
		}
	}
}
