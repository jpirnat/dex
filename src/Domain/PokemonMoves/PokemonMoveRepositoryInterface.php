<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface PokemonMoveRepositoryInterface
{
	/**
	 * Get Pokémon moves by Pokémon, in this generation and earlier. Does not
	 * include the typed Hidden Powers, or moves learned via Sketch.
	 *
	 * @return PokemonMove[] Ordered by level, then sort, for easier parsing by
	 *     DexPokemonMovesModel.
	 */
	public function getByPokemonAndVersionGroup(
		PokemonId $pokemonId,
		VersionGroupId $versionGroupId,
	) : array;

	/**
	 * Get Pokémon moves by move, in this generation and earlier. Does not
	 * include moves learned via Sketch.
	 *
	 * @return PokemonMove[]
	 */
	public function getByMoveAndVersionGroup(
		MoveId $moveId,
		VersionGroupId $versionGroupId,
	) : array;
}
