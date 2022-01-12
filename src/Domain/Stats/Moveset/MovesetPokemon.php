<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidViabilityCeilingException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

final class MovesetPokemon
{
	use ValidateMonthTrait;

	/**
	 * Constructor.
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidCountException if $rawCount is invalid.
	 * @throws InvalidViabilityCeilingException if $viabilityCeiling is invalid.
	 */
	public function __construct(
		private DateTime $month,
		private FormatId $formatId,
		private PokemonId $pokemonId,
		private int $rawCount,
		private ?int $viabilityCeiling,
	) {
		$this->validateMonth($month);

		if ($rawCount < 0) {
			throw new InvalidCountException('Invalid raw count: ' . $rawCount);
		}

		if ($viabilityCeiling !== null && $viabilityCeiling < 0) {
			throw new InvalidViabilityCeilingException(
				'Invalid viability ceiling: ' . $viabilityCeiling
			);
		}
	}

	/**
	 * Get the month.
	 */
	public function getMonth() : DateTime
	{
		return $this->month;
	}

	/**
	 * Get the format id.
	 */
	public function getFormatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the Pokémon id.
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the raw count.
	 */
	public function getRawCount() : int
	{
		return $this->rawCount;
	}

	/**
	 * Get the viability ceiling.
	 */
	public function getViabilityCeiling() : ?int
	{
		return $this->viabilityCeiling;
	}
}
