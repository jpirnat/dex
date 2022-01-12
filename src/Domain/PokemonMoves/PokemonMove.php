<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class PokemonMove
{
	public function __construct(
		private PokemonId $pokemonId,
		private VersionGroupId $versionGroupId,
		private MoveId $moveId,
		private MoveMethodId $moveMethodId,
		private int $level,
		private int $sort,
	) {}

	/**
	 * Get the Pokémon move's Pokémon id.
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the Pokémon move's version group id.
	 */
	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
	}

	/**
	 * Get the Pokémon move's move id.
	 */
	public function getMoveId() : MoveId
	{
		return $this->moveId;
	}

	/**
	 * Get the Pokémon move's move method id.
	 */
	public function getMoveMethodId() : MoveMethodId
	{
		return $this->moveMethodId;
	}

	/**
	 * Get the Pokémon move's level.
	 */
	public function getLevel() : int
	{
		return $this->level;
	}

	/**
	 * Get the Pokémon move's sort value.
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
}
