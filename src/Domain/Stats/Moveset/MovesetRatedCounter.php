<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidYearException;

class MovesetRatedCounter
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

	/** @var PokemonId $counterId */
	private $counterId;

	/** @var float $number1 */
	private $number1;

	/** @var float $number2 */
	private $number2;

	/** @var float $number3 */
	private $number3;

	/** @var float $percentKnockedOut */
	private $percentKnockedOut;

	/** @var float $percentSwitchedOut */
	private $percentSwitchedOut;

	/**
	 * Constructor.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param PokemonId $counterId
	 * @param float $number1
	 * @param float $number2
	 * @param float $number3
	 * @param float $percentKnockedOut
	 * @param float $percentSwitchedOut
	 *
	 * @throws InvalidYearException if $year is invalid.
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidPercentException if $percentKnockedOut is invalid or if
	 *     $percentSwitchedOut is invalid.
	 */
	public function __construct(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		PokemonId $counterId,
		float $number1,
		float $number2,
		float $number3,
		float $percentKnockedOut,
		float $percentSwitchedOut
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

		// TODO: validation for number1, number2, and number3.

		if ($percentKnockedOut < 0 || $percentKnockedOut > 100) {
			throw new InvalidPercentException(
				'Invalid percent knocked out: ' . $percentKnockedOut
			);
		}

		if ($percentSwitchedOut < 0 || $percentSwitchedOut > 100) {
			throw new InvalidPercentException(
				'Invalid percent switched out: ' . $percentSwitchedOut
			);
		}

		$this->year = $year;
		$this->month = $month;
		$this->formatId = $formatId;
		$this->rating = $rating;
		$this->pokemonId = $pokemonId;
		$this->counterId = $counterId;
		$this->number1 = $number1;
		$this->number2 = $number2;
		$this->number3 = $number3;
		$this->percentKnockedOut = $percentKnockedOut;
		$this->percentSwitchedOut = $percentSwitchedOut;
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
	 * Get the Pokémon id of the counter.
	 *
	 * @return PokemonId
	 */
	public function getCounterId() : PokemonId
	{
		return $this->counterId;
	}

	/**
	 * Get the number1.
	 *
	 * @return float
	 */
	public function getNumber1() : float
	{
		return $this->number1;
	}

	/**
	 * Get the number2.
	 *
	 * @return float
	 */
	public function getNumber2() : float
	{
		return $this->number2;
	}

	/**
	 * Get the number3.
	 *
	 * @return float
	 */
	public function getNumber3() : float
	{
		return $this->number3;
	}

	/**
	 * Get the percent knocked out.
	 *
	 * @return float
	 */
	public function getPercentKnockedOut() : float
	{
		return $this->percentKnockedOut;
	}

	/**
	 * Get the percent switched out.
	 *
	 * @return float
	 */
	public function getPercentSwitchedOut() : float
	{
		return $this->percentSwitchedOut;
	}
}
