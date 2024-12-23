<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

/**
 * This class holds data derived from averaging a Pokémon's leads Pokémon data
 * over a span of multiple months.
 */
final readonly class LeadsAveragedPokemon
{
	use ValidateMonthTrait;

	/**
	 * @throws InvalidMonthException if $start or $end is invalid.
	 * @throws InvalidCountException if $raw is invalid.
	 * @throws InvalidPercentException if $rawPercent is invalid.
	 */
	public function __construct(
		private(set) DateTime $start,
		private(set) DateTime $end,
		private(set) FormatId $formatId,
		private(set) PokemonId $pokemonId,
		private(set) int $raw,
		private(set) float $rawPercent,
	) {
		$this->validateMonth($start);
		$this->validateMonth($end);

		if ($raw < 0) {
			throw new InvalidCountException("Invalid raw: $raw.");
		}

		if ($rawPercent < 0 || $rawPercent > 100) {
			throw new InvalidPercentException("Invalid raw percent: $rawPercent.");
		}
	}
}
