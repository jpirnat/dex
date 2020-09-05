<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final class MovesetRatedMove
{
	private UsageRatedPokemonId $usageRatedPokemonId;
	private MoveId $moveId;
	private float $percent;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonId $usageRatedPokemonId
	 * @param MoveId $moveId
	 * @param float $percent
	 *
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		UsageRatedPokemonId $usageRatedPokemonId,
		MoveId $moveId,
		float $percent
	) {
		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException('Invalid percent: ' . $percent);
		}

		$this->usageRatedPokemonId = $usageRatedPokemonId;
		$this->moveId = $moveId;
		$this->percent = $percent;
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
	 * Get the move id.
	 *
	 * @return MoveId
	 */
	public function getMoveId() : MoveId
	{
		return $this->moveId;
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
