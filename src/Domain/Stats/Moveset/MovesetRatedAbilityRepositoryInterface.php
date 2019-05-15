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
	 * Get moveset rated ability records by their format, rating, Pokémon, and ability.
	 * Use this to create a trend line for a Pokémon's ability usage in a format.
	 * Indexed and sorted by month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param AbilityId $abilityId
	 *
	 * @return MovesetRatedAbility[]
	 */
	public function getByFormatAndRatingAndPokemonAndAbility(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		AbilityId $abilityId
	) : array;
}
