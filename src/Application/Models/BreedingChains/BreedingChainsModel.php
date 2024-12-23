<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\BreedingChains;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\BreedingChains\BreedingChainFinder;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\PokemonMove;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveFormatter;
use Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface;

final class BreedingChainsModel
{
	private(set) array $pokemon = [];
	private(set) array $move = [];

	/** @var BreedingChainRecord[][] $chains */
	private(set) array $chains = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly MoveRepositoryInterface $moveRepository,
		private readonly BreedingChainFinder $breedingChainFinder,
		private readonly DexVersionGroupRepositoryInterface $dexVersionGroupRepository,
		private readonly PokemonNameRepositoryInterface $pokemonNameRepository,
		private readonly MoveNameRepositoryInterface $moveNameRepository,
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
		private readonly PokemonMoveFormatter $pokemonMoveFormatter,
	) {}


	/**
	 * Set breeding chain data for this Pokémon, move, and version group combination.
	 */
	public function setData(
		string $vgIdentifier,
		string $pokemonIdentifier,
		string $moveIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		$move = $this->moveRepository->getByIdentifier($moveIdentifier);

		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemon->id,
		);
		$this->pokemon = [
			'identifier' => $pokemon->identifier,
			'name' => $pokemonName->name,
		];

		$moveName = $this->moveNameRepository->getByLanguageAndMove($languageId, $move->id);
		$this->move = [
			'name' => $moveName->name,
		];

		$chains = $this->breedingChainFinder->findChains(
			$versionGroupId,
			$pokemon->id,
			$move->id,
		);

		$this->chains = [];
		foreach ($chains as $chain) {
			$chainId = [];
			$records = [];
			foreach ($chain as $pokemonMove) {
				$chainId[] = $pokemonMove->pokemonId->value();
				$records[] = $this->getRecord($pokemonMove, $languageId);
			}
			$chainId = implode('-', $chainId);
			$this->chains[$chainId] = $records;
		}
	}

	/**
	 * Create the breeding chain record for this Pokémon move.
	 */
	private function getRecord(
		PokemonMove $pokemonMove,
		LanguageId $languageId,
	) : BreedingChainRecord {
		$versionGroup = $this->dexVersionGroupRepository->getById(
			$pokemonMove->versionGroupId,
			$languageId,
		);

		$pokemon = $this->dexPokemonRepository->getById(
			$versionGroup->getId(),
			$pokemonMove->pokemonId,
			$languageId,
		);

		return new BreedingChainRecord(
			$pokemon->icon,
			$pokemon->identifier,
			$pokemon->name,
			$versionGroup,
			$pokemon->eggGroups,
			$pokemon->genderRatio,
			$pokemon->eggCycles,
			$pokemon->stepsToHatch,
			$this->pokemonMoveFormatter->format($pokemonMove, $languageId),
		);
	}
}

