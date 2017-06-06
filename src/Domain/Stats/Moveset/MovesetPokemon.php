<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidViabilityCeilingException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidYearException;

class MovesetPokemon
{
	/** @var int $year */
	private $year;

	/** @var int $month */
	private $month;

	/** @var FormatId $formatId */
	private $formatId;

	/** @var PokemonId $pokemonId */
	private $pokemonId;

	/** @var int $rawCount */
	private $rawCount;

	/** @var int|null $viabilityCeiling */
	private $viabilityCeiling;

	/**
	 * Constructor.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 * @param int $rawCount
	 * @param int|null $viabilityCeiling
	 *
	 * @throws InvalidYearException if $year is invalid.
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidCountException if $rawCount is invalid.
	 * @throws InvalidViabilityCeilingException if $viabilityCeiling is invalid.
	 */
	public function __construct(
		int $year,
		int $month,
		FormatId $formatId,
		PokemonId $pokemonId,
		int $rawCount,
		?int $viabilityCeiling
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

		if ($rawCount < 0) {
			throw new InvalidCountException('Invalid raw count: ' . $rawCount);
		}

		if ($viabilityCeiling !== null && $viabilityCeiling < 0) {
			throw new InvalidViabilityCeilingException(
				'Invalid viability ceiling: ' . $viabilityCeiling
			);
		}

		$this->year = $year;
		$this->month = $month;
		$this->formatId = $formatId;
		$this->pokemonId = $pokemonId;
		$this->rawCount = $rawCount;
		$this->viabilityCeiling = $viabilityCeiling;
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
	 * Get the raw count.
	 *
	 * @return int
	 */
	public function getRawCount() : int
	{
		return $this->rawCount;
	}

	/**
	 * Get the viability ceiling.
	 *
	 * @return int|null
	 */
	public function getViabilityCeiling() : ?int
	{
		return $this->viabilityCeiling;
	}
}
