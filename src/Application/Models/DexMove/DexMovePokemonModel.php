<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Jp\Dex\Application\Models\StatNameModel;
use Jp\Dex\Domain\Items\ItemDescriptionRepositoryInterface;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodNameRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\MoveMethodRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\VersionGroup;

final class DexMovePokemonModel
{
	private array $stats = [];

	/** @var DexMovePokemonMethod[] $methods */
	private array $methods = [];


	public function __construct(
		private PokemonMoveRepositoryInterface $pokemonMoveRepository,
		private TmRepositoryInterface $tmRepository,
		private ItemDescriptionRepositoryInterface $itemDescriptionRepository,
		private DexPokemonRepositoryInterface $dexPokemonRepository,
		private MoveMethodRepositoryInterface $moveMethodRepository,
		private MoveMethodNameRepositoryInterface $moveMethodNameRepository,
		private StatNameModel $statNameModel,
	) {}


	/**
	 * Set data for the dex move page's Pokémon table.
	 *
	 * @param DexVersionGroup[] $versionGroups
	 */
	public function setData(
		MoveId $moveId,
		VersionGroup $versionGroup,
		LanguageId $languageId,
		array $versionGroups
	) : void {
		$pokemonMoves = $this->pokemonMoveRepository->getByMoveAndGeneration(
			$moveId,
			$versionGroup->getGenerationId(),
		);

		// Get data for the real Hidden Power instead of typed Hidden Powers.
		if (MoveId::TYPED_HIDDEN_POWER_BEGIN <= $moveId->value()
			&& $moveId->value() <= MoveId::TYPED_HIDDEN_POWER_END
		) {
			$moveId = new MoveId(MoveId::HIDDEN_POWER);
		}

		// Get all the TMs that could show up in the table.
		$tms = $this->tmRepository->getByMove($moveId);
		$beginVg = $versionGroups[array_key_first($versionGroups)];
		$endVg = $versionGroups[array_key_last($versionGroups)];
		$itemDescriptions = $this->itemDescriptionRepository->getByTmMoveBetween(
			$beginVg->getGenerationId(),
			$endVg->getGenerationId(),
			$moveId,
			$languageId,
		);

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
					$itemId = $tm->getItemId()->value();
					$itemDescription = $itemDescriptions[$vgId][$itemId];

					$methodsPokemons[$methodId][$pokemonId][$vgIdentifier] = $itemDescription->getName();
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
			$versionGroup->getId(),
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
					$pokemon->getIcon(),
					$pokemon->getIdentifier(),
					$pokemon->getName(),
					$pokemon->getTypes(),
					$pokemon->getAbilities(),
					$pokemon->getBaseStats(),
					$pokemon->getBst(),
					$pokemon->getSort(),
				);
			}
		}

		// Get other data for the dex move Pokémon method records.
		$moveMethods = $this->moveMethodRepository->getAll();
		$moveMethodNames = $this->moveMethodNameRepository->getByLanguage(
			$languageId
		);

		// Get stat name abbreviations.
		$this->stats = $this->statNameModel->getByVersionGroup($versionGroup->getId(), $languageId);

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
				$dexMovePokemon[$methodId],
			);
		}
	}


	/**
	 * Get the stats and their names.
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
