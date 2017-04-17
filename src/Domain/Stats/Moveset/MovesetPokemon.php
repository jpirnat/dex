<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

class MovesetPokemon
{
	/** @var int $year */
	protected $year;

	/** @var int $month */
	protected $month;

	/** @var FormatId $formatId */
	protected $formatId;

	/** @var PokemonId $pokemonId */
	protected $pokemonId;

	/** @var int $rawCount */
	protected $rawCount;

	/** @var int|null $viabilityCeiling */
	protected $viabilityCeiling;

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
	 * Get the Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function pokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the raw count.
	 *
	 * @return int
	 */
	public function rawCount() : int
	{
		return $this->rawCount;
	}

	/**
	 * Get the viability ceiling.
	 *
	 * @return int|null
	 */
	public function viabilityCeiling() : ?int
	{
		return $this->viabilityCeiling;
	}
}
