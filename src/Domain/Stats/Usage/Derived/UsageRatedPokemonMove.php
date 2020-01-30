<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Derived;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\Stats\UsageDataInterface;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

/**
 * This class holds data derived from the join of a usage rated Pokémon record
 * and a moveset rated move record. The $pokemonPercent property should come
 * from `usage_rated_pokemon`.`usage_percent`. The $movePercent property should
 * come from `moveset_rated_moves`.`percent`. The $usagePercent property should
 * be those two values multiplied together (and divided by 100 to keep it a
 * percent).
 */
final class UsageRatedPokemonMove implements UsageDataInterface
{
	use ValidateMonthTrait;

	private DateTime $month;
	private FormatId $formatId;
	private int $rating;
	private PokemonId $pokemonId;
	private float $pokemonPercent;
	private MoveId $moveId;
	private float $movePercent;
	private float $usagePercent;

	/**
	 * Constructor.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param float $pokemonPercent
	 * @param MoveId $moveId
	 * @param float $movePercent
	 * @param float $usagePercent
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidPercentException if $pokemonPercent, $movePercent, or
	 *     $usagePercent is invalid.
	 */
	public function __construct(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		float $pokemonPercent,
		MoveId $moveId,
		float $movePercent,
		float $usagePercent
	) {
		$this->validateMonth($month);

		if ($rating < 0) {
			throw new InvalidRatingException('Invalid rating: ' . $rating);
		}

		if ($pokemonPercent < 0 || $pokemonPercent > 100) {
			throw new InvalidPercentException(
				'Invalid Pokémon percent: ' . $pokemonPercent
			);
		}

		if ($movePercent < 0 || $movePercent > 100) {
			throw new InvalidPercentException(
				'Invalid move percent: ' . $movePercent
			);
		}

		if ($usagePercent < 0 || $usagePercent > 100) {
			throw new InvalidPercentException(
				'Invalid usage percent: ' . $usagePercent
			);
		}

		$this->month = $month;
		$this->formatId = $formatId;
		$this->rating = $rating;
		$this->pokemonId = $pokemonId;
		$this->pokemonPercent = $pokemonPercent;
		$this->moveId = $moveId;
		$this->movePercent = $movePercent;
		$this->usagePercent = $usagePercent;
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
	 * Get the usage percent of the Pokémon.
	 *
	 * @return float
	 */
	public function getPokemonPercent() : float
	{
		return $this->pokemonPercent;
	}

	/**
	 * Get the move id.
	 *
	 * @return MoveId
	 */
	public function getMoveId() : MoveId
	{
		return $this->moveId;
	}

	/**
	 * Get the usage percent of the move on the Pokémon.
	 *
	 * @return float
	 */
	public function getMovePercent() : float
	{
		return $this->movePercent;
	}

	/**
	 * Get the usage percent of the Pokémon and move combination.
	 *
	 * @return float
	 */
	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}
}
