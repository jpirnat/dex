<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

/**
 * This class holds data derived from averaging a Pokémon's usage Pokémon data
 * over a span of multiple months.
 */
final readonly class UsageAveragedPokemon
{
	use ValidateMonthTrait;

	/**
	 * Constructor.
	 *
	 * @throws InvalidMonthException if $start or $end is invalid.
	 * @throws InvalidCountException if $raw is invalid or if $real is invalid.
	 * @throws InvalidPercentException if $rawPercent is invalid or if
	 *     $realPercent is invalid.
	 */
	public function __construct(
		private DateTime $start,
		private DateTime $end,
		private FormatId $formatId,
		private PokemonId $pokemonId,
		private int $raw,
		private float $rawPercent,
		private int $real,
		private float $realPercent,
	) {
		$this->validateMonth($start);
		$this->validateMonth($end);

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

	/**
	 * Get the start month.
	 */
	public function getStart() : DateTime
	{
		return $this->start;
	}

	/**
	 * Get the end month.
	 */
	public function getEnd() : DateTime
	{
		return $this->end;
	}

	/**
	 * Get the format id.
	 */
	public function getFormatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the Pokémon id.
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the raw.
	 */
	public function getRaw() : int
	{
		return $this->raw;
	}

	/**
	 * Get the raw percent.
	 */
	public function getRawPercent() : float
	{
		return $this->rawPercent;
	}

	/**
	 * Get the real.
	 */
	public function getReal() : int
	{
		return $this->real;
	}

	/**
	 * Get the real percent.
	 */
	public function getRealPercent() : float
	{
		return $this->realPercent;
	}
}
