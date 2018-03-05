<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Derived;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface UsageRatedPokemonItemRepositoryInterface
{
	/**
	 * Get usage rated Pokémon item records by their year, month, format,
	 * rating, and item. Indexed by Pokémon id value.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param ItemId $itemId
	 *
	 * @return UsageRatedPokemonItem[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndItem(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		ItemId $itemId
	) : array;

	/**
	 * Get usage rated Pokémon item records by their format, rating, Pokémon,
	 * and item. Use this to create a trend line for the usage of a specific
	 * Pokémon with a specific item. Indexed and sorted by year then month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param ItemId $itemId
	 *
	 * @return UsageRatedPokemonItem[][]
	 */
	public function getByFormatAndRatingAndPokemonAndItem(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId
	) : array;
}
