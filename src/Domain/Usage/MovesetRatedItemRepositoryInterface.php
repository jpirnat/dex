<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

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
	 * Get moveset rated item records by format and rating and Pokémon.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedItem[]
	 */
	public function getByFormatAndRatingAndPokemon(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array;

	/**
	 * Get moveset rated item records by format and Pokémon and item.
	 *
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 * @param ItemId $itemId
	 *
	 * @return MovesetRatedItem[]
	 */
	public function getByFormatAndPokemonAndItem(
		FormatId $formatId,
		PokemonId $pokemonId,
		ItemId $itemId
	) : array;
}
