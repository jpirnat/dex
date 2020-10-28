<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

final class PokemonType
{
	public function __construct(
		private GenerationId $generationId,
		private PokemonId $pokemonId,
		private int $slot,
		private TypeId $typeId,
	) {}

	/**
	 * Get the Pokémon type's generation id.
	 *
	 * @return GenerationId
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the Pokémon type's Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the Pokémon type's slot.
	 *
	 * @return int
	 */
	public function getSlot() : int
	{
		return $this->slot;
	}

	/**
	 * Get the Pokémon type's type id.
	 *
	 * @return TypeId
	 */
	public function getTypeId() : TypeId
	{
		return $this->typeId;
	}
}
