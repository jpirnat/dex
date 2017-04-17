<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

class MovesetRatedPokemon
{
	/** @var int $year */
	protected $year;

	/** @var int $month */
	protected $month;

	/** @var FormatId $formatId */
	protected $formatId;

	/** @var int $rating */
	protected $rating;

	/** @var PokemonId $pokemonId */
	protected $pokemonId;

	/** @var float $averageWeight */
	protected $averageWeight;

	/**
	 * Constructor.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param float $averageWeight
	 */
	public function __construct(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		float $averageWeight
	) {
		// TODO: validation

		$this->year = $year;
		$this->month = $month;
		$this->formatId = $formatId;
		$this->rating = $rating;
		$this->pokemonId = $pokemonId;
		$this->averageWeight = $averageWeight;
	}

	/**
	 * Get the year.
	 *
	 * @return int
	 */
	public function year() : int
	{
		return $this->year;
	}

	/**
	 * Get the month.
	 *
	 * @return int
	 */
	public function month() : int
	{
		return $this->month;
	}

	/**
	 * Get the format id.
	 *
	 * @return FormatId
	 */
	public function formatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the rating.
	 *
	 * @return int
	 */
	public function rating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function pokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the average weight.
	 *
	 * @return float
	 */
	public function averageWeight() : float
	{
		return $this->averageWeight;
	}
}
