<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Derived;

use DateTime;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\Stats\UsageDataInterface;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

/**
 * This class holds data derived from the join of a usage rated Pokémon record
 * and a moveset rated ability record. The $pokemonPercent property should come
 * from `usage_rated_pokemon`.`usage_percent`. The $abilityPercent property
 * should come from `moveset_rated_abilities`.`percent`. The $usagePercent
 * property should be those two values multiplied together (and divided by 100
 * to keep it a percent).
 */
final class UsageRatedPokemonAbility implements UsageDataInterface
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

	/** @var float $pokemonPercent */
	private $pokemonPercent;

	/** @var AbilityId $abilityId */
	private $abilityId;

	/** @var float $abilityPercent */
	private $abilityPercent;

	/** @var float $usagePercent */
	private $usagePercent;

	/**
	 * Constructor.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param float $pokemonPercent
	 * @param AbilityId $abilityId
	 * @param float $abilityPercent
	 * @param float $usagePercent
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidPercentException if $pokemonPercent, $abilityPercent, or
	 *     $usagePercent is invalid.
	 */
	public function __construct(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		float $pokemonPercent,
		AbilityId $abilityId,
		float $abilityPercent,
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

		if ($abilityPercent < 0 || $abilityPercent > 100) {
			throw new InvalidPercentException(
				'Invalid ability percent: ' . $abilityPercent
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
		$this->abilityId = $abilityId;
		$this->abilityPercent = $abilityPercent;
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
	 * Get the ability id.
	 *
	 * @return AbilityId
	 */
	public function getAbilityId() : AbilityId
	{
		return $this->abilityId;
	}

	/**
	 * Get the usage percent of the ability on the Pokémon.
	 *
	 * @return float
	 */
	public function getAbilityPercent() : float
	{
		return $this->abilityPercent;
	}

	/**
	 * Get the usage percent of the Pokémon and ability combination.
	 *
	 * @return float
	 */
	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}
}
