<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\StatValueContainer;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final readonly class MovesetRatedSpread
{
	/**
	 * Constructor.
	 *
	 * @throws InvalidCountException if any EV spread values are invalid.
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		private UsageRatedPokemonId $usageRatedPokemonId,
		private NatureId $natureId,
		private StatValueContainer $evSpread,
		private float $percent,
	) {
		foreach ($evSpread->getAll() as $statValue) {
			if ($statValue->getValue() < 0 || $statValue->getValue() > 255) {
				$statId = $statValue->getStatId()->value();
				throw new InvalidCountException(
					"Invalid number of EVs for stat id $statId."
				);
			}
		}

		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException("Invalid percent: $percent.");
		}
	}

	/**
	 * Get the usage rated Pokémon id.
	 */
	public function getUsageRatedPokemonId() : UsageRatedPokemonId
	{
		return $this->usageRatedPokemonId;
	}

	/**
	 * Get the nature id.
	 */
	public function getNatureId() : NatureId
	{
		return $this->natureId;
	}

	/**
	 * Get the EV spread.
	 */
	public function getEvSpread() : StatValueContainer
	{
		return $this->evSpread;
	}

	/**
	 * Get the percent.
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
