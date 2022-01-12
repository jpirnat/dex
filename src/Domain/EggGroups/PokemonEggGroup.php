<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

use Jp\Dex\Domain\Pokemon\PokemonId;

final class PokemonEggGroup
{
	public function __construct(
		private PokemonId $pokemonId,
		private EggGroupId $eggGroupId,
	) {}

	/**
	 * Get the Pokémon egg group's Pokémon id.
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the Pokémon egg group's egg group id.
	 */
	public function getEggGroupId() : EggGroupId
	{
		return $this->eggGroupId;
	}
}
