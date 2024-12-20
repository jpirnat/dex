<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsPokemon;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\ExpandedDexPokemon;
use Jp\Dex\Domain\Pokemon\ExpandedDexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\VgPokemonNotFoundException;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class PokemonModel
{
	private(set) ?ExpandedDexPokemon $pokemon;
	private(set) array $stats = [];


	public function __construct(
		private readonly ExpandedDexPokemonRepositoryInterface $expandedDexPokemonRepository,
		private readonly DexStatRepositoryInterface $dexStatRepository,
	) {}


	/**
	 * Set miscellaneous data about the Pokémon (name, types, base stats, etc).
	 */
	public function setData(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : void {
		$this->pokemon = null;
		$this->stats = [];

		try {
			$this->pokemon = $this->expandedDexPokemonRepository->getById(
				$versionGroupId,
				$pokemonId,
				$languageId,
			);
		} catch (VgPokemonNotFoundException) {
			return;
		}

		$this->stats = $this->dexStatRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
	}
}
