<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Versions\VersionGroupId;

interface VgPokemonRepositoryInterface
{
	/**
	 * Get a version group Pokémon by version group and Pokémon.
	 *
	 * @throws VgPokemonNotFoundException if no version group Pokémon
	 *     exists for this version group and Pokémon.
	 */
	public function getByVgAndPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
	): VgPokemon;
}
