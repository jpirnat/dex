<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface MovesetRatedAbilityRepositoryInterface
{
	/**
	 * Save a moveset rated ability record.
	 *
	 * @param MovesetRatedAbility $movesetRatedAbility
	 *
	 * @return void
	 */
	public function save(MovesetRatedAbility $movesetRatedAbility) : void;

	/**
	 * Get moveset rated ability records by year, month, format, rating, and
	 * Pokémon. Indexed by ability id value.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedAbility[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndPokemon(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array;

	/**
	 * Get moveset rated ability records by format and rating and Pokémon.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedAbility[]
	 */
	public function getByFormatAndRatingAndPokemon(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array;

	/**
	 * Get moveset rated ability records by format and Pokémon and ability.
	 *
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 * @param AbilityId $abilityId
	 *
	 * @return MovesetRatedAbility[]
	 */
	public function getByFormatAndPokemonAndAbility(
		FormatId $formatId,
		PokemonId $pokemonId,
		AbilityId $abilityId
	) : array;
}
