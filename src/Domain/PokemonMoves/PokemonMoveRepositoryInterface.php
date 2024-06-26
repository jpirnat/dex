<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface PokemonMoveRepositoryInterface
{
	/**
	 * Get Pokémon moves available for this Pokémon in this version group, based
	 * on all the version groups that can transfer movesets into this one.
	 *
	 * @return PokemonMove[] Ordered by level, then sort, for easier parsing by
	 *     DexPokemonMovesModel.
	 */
	public function getByIntoVgAndPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
	) : array;

	/**
	 * Get Pokémon moves available for this move in this version group,
	 * based on all the version groups that can transfer movesets into this one.
	 * Does not include moves learned via Sketch.
	 *
	 * @return PokemonMove[]
	 */
	public function getByIntoVgAndMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
	) : array;
}
