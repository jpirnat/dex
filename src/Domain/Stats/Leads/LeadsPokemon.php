<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidYearException;

class LeadsPokemon
{
	/** @var int $year */
	private $year;

	/** @var int $month */
	private $month;

	/** @var FormatId $formatId */
	private $formatId;

	/** @var PokemonId $pokemonId */
	private $pokemonId;

	/** @var int $raw */
	private $raw;

	/** @var float $rawPercent */
	private $rawPercent;

	/**
	 * Constructor.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 * @param int $raw
	 * @param float $rawPercent
	 *
	 * @throws InvalidYearException if $year is invalid.
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidCountException if $raw is invalid.
	 * @throws InvalidPercentException if $rawPercent is invalid.
	 */
	public function __construct(
		int $year,
		int $month,
		FormatId $formatId,
		PokemonId $pokemonId,
		int $raw,
		float $rawPercent
	) {
		$today = new DateTime();
		$currentYear = (int) $today->format('Y');
		$currentMonth = (int) $today->format('n');

		if ($year < 2014) {
			throw new InvalidYearException('Invalid year: ' . $year);
		}

		if ($year > $currentYear) {
			throw new InvalidYearException(
				'This year has not happened yet: ' . $year
			);
		}

		if ($month < 1 || $month > 12) {
			throw new InvalidMonthException('Invalid month: ' . $month);
		}

		if ($year === $currentYear && $month > $currentMonth) {
			throw new InvalidMonthException(
				'This month has not happened yet: ' . $month
			);
		}

		if ($raw < 0) {
			throw new InvalidCountException('Invalid raw: ' . $raw);
		}

		if ($rawPercent < 0 || $rawPercent > 100) {
			throw new InvalidPercentException(
				'Invalid raw percent: ' . $rawPercent
			);
		}

		$this->year = $year;
		$this->month = $month;
		$this->formatId = $formatId;
		$this->pokemonId = $pokemonId;
		$this->raw = $raw;
		$this->rawPercent = $rawPercent;
	}

	/**
	 * Get the year.
	 *
	 * @return int
	 */
	public function getYear() : int
	{
		return $this->year;
	}

	/**
	 * Get the month.
	 *
	 * @return int
	 */
	public function getMonth() : int
	{
		return $this->month;
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
