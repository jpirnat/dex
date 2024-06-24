<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset\Averaged;

use DateTime;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

/**
 * This class holds data derived from averaging an ability's usage percent over
 * a span of multiple months.
 */
final readonly class MovesetRatedAveragedAbility
{
	use ValidateMonthTrait;

	/**
	 * Constructor.
	 *
	 * @throws InvalidMonthException if $start or $end is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		private DateTime $start,
		private DateTime $end,
		private FormatId $formatId,
		private int $rating,
		private PokemonId $pokemonId,
		private AbilityId $abilityId,
		private float $percent,
	) {
		$this->validateMonth($start);
		$this->validateMonth($end);

		if ($rating < 0) {
			throw new InvalidRatingException("Invalid rating: $rating.");
		}

		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException("Invalid percent: $percent.");
		}
	}

	/**
	 * Get the start month.
	 */
	public function getStart() : DateTime
	{
		return $this->start;
	}

	/**
	 * Get the end month.
	 */
	public function getEnd() : DateTime
	{
		return $this->end;
	}

	/**
	 * Get the format id.
	 */
	public function getFormatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the rating.
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the Pokémon id.
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the ability id.
	 */
	public function getAbilityId() : AbilityId
	{
		return $this->abilityId;
	}

	/**
	 * Get the percent.
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
