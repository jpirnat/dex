<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface MovesetRatedAveragedAbilityRepositoryInterface
{
	/**
	 * Get moveset rated averaged ability records by their start month, end month,
	 * format, rating, and Pokémon.
	 *
	 * @return MovesetRatedAveragedAbility[] Indexed by ability id.
	 */
	public function getByMonthsAndFormatAndRatingAndPokemon(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array;
}
