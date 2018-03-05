<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Derived;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface UsageRatedPokemonAbilityRepositoryInterface
{
	/**
	 * Get usage rated Pokémon ability records by their year, month, format,
	 * rating, and ability. Indexed by Pokémon id value.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param AbilityId $abilityId
	 *
	 * @return UsageRatedPokemonAbility[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndAbility(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		AbilityId $abilityId
	) : array;

	/**
	 * Get usage rated Pokémon ability records by their format, rating, Pokémon,
	 * and ability. Use this to create a trend line for the usage of a specific
	 * Pokémon with a specific ability. Indexed and sorted by year then month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param AbilityId $abilityId
	 *
	 * @return UsageRatedPokemonAbility[][]
	 */
	public function getByFormatAndRatingAndPokemonAndAbility(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		AbilityId $abilityId
	) : array;
}
