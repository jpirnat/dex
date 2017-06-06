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

	/** @var int $hp */
	private $hp;

	/** @var int $atk */
	private $atk;

	/** @var int $def */
	private $def;

	/** @var int $spa */
	private $spa;

	/** @var int $spd */
	private $spd;

	/** @var int $spe */
	private $spe;

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
	 * @param int $hp
	 * @param int $atk
	 * @param int $def
	 * @param int $spa
	 * @param int $spd
	 * @param int $spe
	 * @param float $percent
	 *
	 * @throws InvalidYearException if $year is invalid.
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidCountException if $hp, $atk, $def, $spa, $spd, or $spe are
	 *     invalid.
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		NatureId $natureId,
		int $hp,
		int $atk,
		int $def,
		int $spa,
		int $spd,
		int $spe,
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

		if ($hp < 0 || $hp > 255) {
			throw new InvalidCountException('Invalid number of HP EVs: ' . $hp);
		}

		if ($atk < 0 || $atk > 255) {
			throw new InvalidCountException(
				'Invalid number of Attack EVs: ' . $atk
			);
		}

		if ($def < 0 || $def > 255) {
			throw new InvalidCountException(
				'Invalid number of Defense EVs: ' . $def
			);
		}

		if ($spa < 0 || $spa > 255) {
			throw new InvalidCountException(
				'Invalid number of Special Attack EVs: ' . $spa
			);
		}

		if ($spd < 0 || $spd > 255) {
			throw new InvalidCountException(
				'Invalid number of Special Defense EVs: ' . $spd
			);
		}

		if ($spe < 0 || $spe > 255) {
			throw new InvalidCountException(
				'Invalid number of Speed EVs: ' . $spe
			);
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
		$this->hp = $hp;
		$this->atk = $atk;
		$this->def = $def;
		$this->spa = $spa;
		$this->spd = $spd;
		$this->spe = $spe;
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
	 * Get the HP EVs.
	 *
	 * @return int
	 */
	public function getHpEvs() : int
	{
		return $this->hp;
	}

	/**
	 * Get the Attack EVs.
	 *
	 * @return int
	 */
	public function getAttackEvs() : int
	{
		return $this->atk;
	}

	/**
	 * Get the Defense EVs.
	 *
	 * @return int
	 */
	public function getDefenseEvs() : int
	{
		return $this->def;
	}

	/**
	 * Get the Special Attack EVs.
	 *
	 * @return int
	 */
	public function getSpecialAttackEvs() : int
	{
		return $this->spa;
	}

	/**
	 * Get the Special Defense EVs.
	 *
	 * @return int
	 */
	public function getSpecialDefenseEvs() : int
	{
		return $this->spd;
	}

	/**
	 * Get the Speed EVs.
	 *
	 * @return int
	 */
	public function getSpeedEvs() : int
	{
		return $this->spe;
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
