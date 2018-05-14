<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Derived;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface UsageRatedPokemonMoveRepositoryInterface
{
	/**
	 * Get usage rated Pokémon move records by their month, format, rating, and
	 * move. Indexed by Pokémon id value.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param MoveId $moveId
	 *
	 * @return UsageRatedPokemonMove[]
	 */
	public function getByMonthAndFormatAndRatingAndMove(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		MoveId $moveId
	) : array;

	/**
	 * Get usage rated Pokémon move records by their format, rating, Pokémon,
	 * and move. Use this to create a trend line for the usage of a specific
	 * Pokémon with a specific move. Indexed and sorted by month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param MoveId $moveId
	 *
	 * @return UsageRatedPokemonMove[]
	 */
	public function getByFormatAndRatingAndPokemonAndMove(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId
	) : array;
}
