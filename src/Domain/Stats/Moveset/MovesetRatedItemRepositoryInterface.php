<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface MovesetRatedItemRepositoryInterface
{
	/**
	 * Save a moveset rated item record.
	 *
	 * @param MovesetRatedItem $movesetRatedItem
	 *
	 * @return void
	 */
	public function save(MovesetRatedItem $movesetRatedItem) : void;

	/**
	 * Get moveset rated item records by month, format, rating, and Pokémon.
	 * Indexed by item id value.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedItem[]
	 */
	public function getByMonthAndFormatAndRatingAndPokemon(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array;

	/**
	 * Get moveset rated item records by their format, rating, Pokémon, and item.
	 * Use this to create a trend line for a Pokémon's item usage in a format.
	 * Indexed and sorted by month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param ItemId $itemId
	 *
	 * @return MovesetRatedItem[]
	 */
	public function getByFormatAndRatingAndPokemonAndItem(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId
	) : array;
}
