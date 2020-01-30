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
final class LeadsAveragedPokemon
{
	use ValidateMonthTrait;

	private DateTime $start;
	private DateTime $end;
	private FormatId $formatId;
	private PokemonId $pokemonId;
	private int $raw;
	private float $rawPercent;

	/**
	 * Constructor.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 * @param int $raw
	 * @param float $rawPercent
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
		float $rawPercent
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

		$this->start = $start;
		$this->end = $end;
		$this->formatId = $formatId;
		$this->pokemonId = $pokemonId;
		$this->raw = $raw;
		$this->rawPercent = $rawPercent;
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
}
