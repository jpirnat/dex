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
final class UsageAveragedPokemon
{
	use ValidateMonthTrait;

	private DateTime $start;
	private DateTime $end;
	private FormatId $formatId;
	private PokemonId $pokemonId;
	private int $raw;
	private float $rawPercent;
	private int $real;
	private float $realPercent;

	/**
	 * Constructor.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 * @param int $raw
	 * @param float $rawPercent
	 * @param int $real
	 * @param float $realPercent
	 *
	 * @throws InvalidMonthException if $start or $end is invalid.
	 * @throws InvalidCountException if $raw is invalid or if $real is invalid.
	 * @throws InvalidPercentException if $rawPercent is invalid or if
	 *     $realPercent is invalid.
	 */
	public function __construct(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		PokemonId $pokemonId,
		int $raw,
		float $rawPercent,
		int $real,
		float $realPercent
	) {
		$this->validateMonth($start);
		$this->validateMonth($end);

		if ($raw < 0) {
			throw new InvalidCountException('Invalid raw: ' . $raw);
		}

		if ($rawPercent < 0 || $rawPercent > 100) {
			throw new InvalidPercentException(
				'Invalid raw percent: ' . $rawPercent
			);
		}

		if ($real < 0) {
			throw new InvalidCountException('Invalid real: ' . $real);
		}

		if ($realPercent < 0 || $realPercent > 100) {
			throw new InvalidPercentException(
				'Invalid real percent: ' . $realPercent
			);
		}

		$this->start = $start;
		$this->end = $end;
		$this->formatId = $formatId;
		$this->pokemonId = $pokemonId;
		$this->raw = $raw;
		$this->rawPercent = $rawPercent;
		$this->real = $real;
		$this->realPercent = $realPercent;
	}

	/**
	 * Get the start month.
	 *
	 * @return DateTime
	 */
	public function getStart() : DateTime
	{
		return $this->start;
	}

	/**
	 * Get the end month.
	 *
	 * @return DateTime
	 */
	public function getEnd() : DateTime
	{
		return $this->end;
	}

	/**
	 * Get the format id.
	 *
	 * @return FormatId
	 */
	public function getFormatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the raw.
	 *
	 * @return int
	 */
	public function getRaw() : int
	{
		return $this->raw;
	}

	/**
	 * Get the raw percent.
	 *
	 * @return float
	 */
	public function getRawPercent() : float
	{
		return $this->rawPercent;
	}

	/**
	 * Get the real.
	 *
	 * @return int
	 */
	public function getReal() : int
	{
		return $this->real;
	}

	/**
	 * Get the real percent.
	 *
	 * @return float
	 */
	public function getRealPercent() : float
	{
		return $this->realPercent;
	}
}
