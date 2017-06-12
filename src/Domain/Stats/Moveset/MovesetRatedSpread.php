<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidYearException;
use Jp\Dex\Domain\Stats\StatValueContainer;

class MovesetRatedSpread
{
	/** @var int $year */
	private $year;

	/** @var int $month */
	private $month;

	/** @var FormatId $formatId */
	private $formatId;

	/** @var int $rating */
	private $rating;

	/** @var PokemonId $pokemonId */
	private $pokemonId;

	/** @var NatureId $natureId */
	private $natureId;

	/** @var StatValueContainer $evSpread */
	private $evSpread;

	/** @var float $percent */
	private $percent;

	/**
	 * Constructor.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param NatureId $natureId
	 * @param StatValueContainer $evSpread
	 * @param float $percent
	 *
	 * @throws InvalidYearException if $year is invalid.
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidCountException if any EV spread values are invalid.
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		NatureId $natureId,
		StatValueContainer $evSpread,
		float $percent
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

		if ($rating < 0) {
			throw new InvalidRatingException('Invalid rating: ' . $rating);
		}

		foreach ($evSpread->getAll() as $statValue) {
			if ($statValue->getValue() < 0 || $statValue->getValue() > 255) {
				throw new InvalidCountException(
					'Invalid number of EVs for stat id '
					. $statValue->getStatId()->value() . '.'
				);
			}
		}

		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException('Invalid percent: ' . $percent);
		}

		$this->year = $year;
		$this->month = $month;
		$this->formatId = $formatId;
		$this->rating = $rating;
		$this->pokemonId = $pokemonId;
		$this->natureId = $natureId;
		$this->evSpread = $evSpread;
		$this->percent = $percent;
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
	 * Get the rating.
	 *
	 * @return int
	 */
	public function getRating() : int
	{
		return $this->rating;
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
	 * Get the nature id.
	 *
	 * @return NatureId
	 */
	public function getNatureId() : NatureId
	{
		return $this->natureId;
	}

	/**
	 * Get the EV spread.
	 *
	 * @return StatValueContainer
	 */
	public function getEvSpread() : StatValueContainer
	{
		return $this->evSpread;
	}

	/**
	 * Get the percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
