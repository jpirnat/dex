<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidAverageWeightException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

final class MovesetRatedPokemon
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

	/** @var float $averageWeight */
	private $averageWeight;

	/**
	 * Constructor.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param float $averageWeight
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidAverageWeightException if $averageWeight is invalid.
	 */
	public function __construct(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		float $averageWeight
	) {
		$this->validateMonth($month);

		if ($rating < 0) {
			throw new InvalidRatingException('Invalid rating: ' . $rating);
		}

		if ($averageWeight < 0) {
			throw new InvalidAverageWeightException(
				'Invalid average weight: ' . $averageWeight
			);
		}

		$this->month = $month;
		$this->formatId = $formatId;
		$this->rating = $rating;
		$this->pokemonId = $pokemonId;
		$this->averageWeight = $averageWeight;
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
	 * Get the average weight.
	 *
	 * @return float
	 */
	public function getAverageWeight() : float
	{
		return $this->averageWeight;
	}
}
