<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface PokemonTypeRepositoryInterface
{
	/**
	 * Get Pokémon's types by version group and Pokémon.
	 *
	 * @return PokemonType[] Indexed and ordered by slot.
	 */
	public function getByVgAndPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
	) : array;
}
