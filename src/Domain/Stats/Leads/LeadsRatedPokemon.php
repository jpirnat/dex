<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRankException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final class LeadsRatedPokemon
{
	private UsageRatedPokemonId $usageRatedPokemonId;
	private int $rank;
	private float $usagePercent;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonId $usageRatedPokemonId
	 * @param int $rank
	 * @param float $usagePercent
	 *
	 * @throws InvalidRankException if $rank is invalid.
	 * @throws InvalidPercentException if $usagePercent is invalid
	 */
	public function __construct(
		UsageRatedPokemonId $usageRatedPokemonId,
		int $rank,
		float $usagePercent
	) {
		if ($rank < 1) {
			throw new InvalidRankException('Invalid rank: ' . $rank);
		}

		if ($usagePercent < 0 || $usagePercent > 100) {
			throw new InvalidPercentException(
				'Invalid usage percent: ' . $usagePercent
			);
		}

		$this->usageRatedPokemonId = $usageRatedPokemonId;
		$this->rank = $rank;
		$this->usagePercent = $usagePercent;
	}

	/**
	 * Get the usage rated Pokémon id.
	 *
	 * @return UsageRatedPokemonId
	 */
	public function getUsageRatedPokemonId() : UsageRatedPokemonId
	{
		return $this->usageRatedPokemonId;
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
