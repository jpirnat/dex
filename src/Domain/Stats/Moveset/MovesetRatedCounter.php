<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

class MovesetRatedCounter
{
	use ValidateMonthTrait;

	/** @var DateTime $month */
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
	 * @param DateTime $month
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
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidPercentException if $percentKnockedOut is invalid or if
	 *     $percentSwitchedOut is invalid.
	 */
	public function __construct(
		DateTime $month,
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
		$this->validateMonth($month);

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
	 * Get the month.
	 *
	 * @return DateTime
	 */
	public function getMonth() : DateTime
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
	 * Get the numeric score for the counter. This is calculated by the formula
	 * (number2 - 4 * number3). According to Antar, it is "an attempt to remove
	 * bias towards low-probability match ups."
	 *
	 * @return float
	 */
	public function getNumber1() : float
	{
		return $this->number1;
	}

	/**
	 * Get the number2. This is the percent of [encounters between the Pokémon
	 * and the counter that ended in one of them being knocked out or switched
	 * out] where the Pokémon is the one that was knocked out or switched out.
	 *
	 * @return float
	 */
	public function getNumber2() : float
	{
		return $this->number2;
	}

	/**
	 * Get the number3. This is the standard deviation for number2.
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
