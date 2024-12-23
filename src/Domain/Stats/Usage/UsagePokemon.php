<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

final readonly class UsagePokemon
{
	use ValidateMonthTrait;

	/**
	 * Constructor.
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidCountException if $raw is invalid or if $real is invalid.
	 * @throws InvalidPercentException if $rawPercent is invalid or if
	 *     $realPercent is invalid.
	 */
	public function __construct(
		private(set) DateTime $month,
		private(set) FormatId $formatId,
		private(set) PokemonId $pokemonId,
		private(set) int $raw,
		private(set) float $rawPercent,
		private(set) int $real,
		private(set) float $realPercent,
	) {
		$this->validateMonth($month);

		if ($raw < 0) {
			throw new InvalidCountException("Invalid raw: $raw.");
		}

		if ($rawPercent < 0 || $rawPercent > 100) {
			throw new InvalidPercentException("Invalid raw percent: $rawPercent.");
		}

		if ($real < 0) {
			throw new InvalidCountException("Invalid real: $real.");
		}

		if ($realPercent < 0 || $realPercent > 100) {
			throw new InvalidPercentException("Invalid real percent: $realPercent.");
		}
	}
}
