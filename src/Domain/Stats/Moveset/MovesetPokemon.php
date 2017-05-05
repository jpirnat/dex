<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

class MovesetPokemon
{
	/** @var int $year */
	private $year;

	/** @var int $month */
	private $month;

	/** @var FormatId $formatId */
	private $formatId;

	/** @var PokemonId $pokemonId */
	private $pokemonId;

	/** @var int $rawCount */
	private $rawCount;

	/** @var int|null $viabilityCeiling */
	private $viabilityCeiling;

	/**
	 * Constructor.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 * @param int $rawCount
	 * @param int|null $viabilityCeiling
	 */
	public function __construct(
		int $year,
		int $month,
		FormatId $formatId,
		PokemonId $pokemonId,
		int $rawCount,
		?int $viabilityCeiling
	) {
		// TODO: validation

		$this->year = $year;
		$this->month = $month;
		$this->formatId = $formatId;
		$this->pokemonId = $pokemonId;
		$this->rawCount = $rawCount;
		$this->viabilityCeiling = $viabilityCeiling;
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
	 * Get the Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the raw count.
	 *
	 * @return int
	 */
	public function getRawCount() : int
	{
		return $this->rawCount;
	}

	/**
	 * Get the viability ceiling.
	 *
	 * @return int|null
	 */
	public function getViabilityCeiling() : ?int
	{
		return $this->viabilityCeiling;
	}
}
