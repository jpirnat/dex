<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

use Jp\Dex\Domain\Pokemon\PokemonId;

final class PokemonEggGroup
{
	private PokemonId $pokemonId;
	private EggGroupId $eggGroupId;

	/**
	 * Constructor.
	 *
	 * @param PokemonId $pokemonId
	 * @param EggGroupId $eggGroupId
	 */
	public function __construct(
		PokemonId $pokemonId,
		EggGroupId $eggGroupId
	) {
		$this->pokemonId = $pokemonId;
		$this->eggGroupId = $eggGroupId;
	}

	/**
	 * Get the Pokémon egg group's Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the Pokémon egg group's egg group id.
	 *
	 * @return EggGroupId
	 */
	public function getEggGroupId() : EggGroupId
	{
		return $this->eggGroupId;
	}
}
