<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

final class LeadsPokemon
{
	use ValidateMonthTrait;

	/** @var DateTime $month */
	private $month;

	/** @var FormatId $formatId */
	private $formatId;

	/** @var PokemonId $pokemonId */
	private $pokemonId;

	/** @var int $raw */
	private $raw;

	/** @var float $rawPercent */
	private $rawPercent;

	/**
	 * Constructor.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 * @param int $raw
	 * @param float $rawPercent
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidCountException if $raw is invalid.
	 * @throws InvalidPercentException if $rawPercent is invalid.
	 */
	public function __construct(
		DateTime $month,
		FormatId $formatId,
		PokemonId $pokemonId,
		int $raw,
		float $rawPercent
	) {
		$this->validateMonth($month);

		if ($raw < 0) {
			throw new InvalidCountException('Invalid raw: ' . $raw);
		}

		if ($rawPercent < 0 || $rawPercent > 100) {
			throw new InvalidPercentException(
				'Invalid raw percent: ' . $rawPercent
			);
		}

		$this->month = $month;
		$this->formatId = $formatId;
		$this->pokemonId = $pokemonId;
		$this->raw = $raw;
		$this->rawPercent = $rawPercent;
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
	 * Get the Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the raw.
	 *
	 * @return int
	 */
	public function getRaw() : int
	{
		return $this->raw;
	}

	/**
	 * Get the raw percent.
	 *
	 * @return float
	 */
	public function getRawPercent() : float
	{
		return $this->rawPercent;
	}
}
