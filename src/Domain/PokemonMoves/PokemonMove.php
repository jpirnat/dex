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
	 *
	 * @return PokemonId
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the Pokémon move's version group id.
	 *
	 * @return VersionGroupId
	 */
	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
	}

	/**
	 * Get the Pokémon move's move id.
	 *
	 * @return MoveId
	 */
	public function getMoveId() : MoveId
	{
		return $this->moveId;
	}

	/**
	 * Get the Pokémon move's move method id.
	 *
	 * @return MoveMethodId
	 */
	public function getMoveMethodId() : MoveMethodId
	{
		return $this->moveMethodId;
	}

	/**
	 * Get the Pokémon move's level.
	 *
	 * @return int
	 */
	public function getLevel() : int
	{
		return $this->level;
	}

	/**
	 * Get the Pokémon move's sort value.
	 *
	 * @return int
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
}
