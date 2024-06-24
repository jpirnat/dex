<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class PokemonType
{
	public function __construct(
		private VersionGroupId $versionGroupId,
		private PokemonId $pokemonId,
		private int $slot,
		private TypeId $typeId,
	) {}

	/**
	 * Get the Pokémon type's generation id.
	 */
	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
	}

	/**
	 * Get the Pokémon type's Pokémon id.
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the Pokémon type's slot.
	 */
	public function getSlot() : int
	{
		return $this->slot;
	}

	/**
	 * Get the Pokémon type's type id.
	 */
	public function getTypeId() : TypeId
	{
		return $this->typeId;
	}
}
