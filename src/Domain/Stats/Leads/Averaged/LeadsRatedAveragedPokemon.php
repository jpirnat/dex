<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads\Averaged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRankException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

/**
 * This class holds data derived from averaging a Pokémon's leads rated Pokémon
 * data over a span of multiple months.
 */
final class LeadsRatedAveragedPokemon
{
	use ValidateMonthTrait;

	/** @var DateTime $start */
	private $start;

	/** @var DateTime $end */
	private $end;

	/** @var FormatId $formatId */
	private $formatId;

	/** @var int $rating */
	private $rating;

	/** @var PokemonId $pokemonId */
	private $pokemonId;

	/** @var int $rank */
	private $rank;

	/** @var float $usagePercent */
	private $usagePercent;

	/**
	 * Constructor.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param int $rank
	 * @param float $usagePercent
	 *
	 * @throws InvalidMonthException if $start or $end is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidRankException if $rank is invalid.
	 * @throws InvalidPercentException if $usagePercent is invalid
	 */
	public function __construct(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		int $rank,
		float $usagePercent
	) {
		$this->validateMonth($start);
		$this->validateMonth($end);

		if ($rating < 0) {
			throw new InvalidRatingException('Invalid rating: ' . $rating);
		}

		if ($rank < 1) {
			throw new InvalidRankException('Invalid rank: ' . $rank);
		}

		if ($usagePercent < 0 || $usagePercent > 100) {
			throw new InvalidPercentException(
				'Invalid usage percent: ' . $usagePercent
			);
		}

		$this->start = $start;
		$this->end = $end;
		$this->formatId = $formatId;
		$this->rating = $rating;
		$this->pokemonId = $pokemonId;
		$this->rank = $rank;
		$this->usagePercent = $usagePercent;
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
	 * Get the rank.
	 *
	 * @return int
	 */
	public function getRank() : int
	{
		return $this->rank;
	}

	/**
	 * Get the usage percent.
	 *
	 * @return float
	 */
	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}
}
