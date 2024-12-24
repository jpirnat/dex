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
		private(set) UsageRatedPokemonId $usageRatedPokemonId,
		private(set) NatureId $natureId,
		private(set) StatValueContainer $evSpread,
		private(set) float $percent,
	) {
		foreach ($evSpread->statValues as $statValue) {
			if ($statValue->value < 0 || $statValue->value > 255) {
				$statId = $statValue->statId->value;
				throw new InvalidCountException(
					"Invalid number of EVs for stat id $statId."
				);
			}
		}

		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException("Invalid percent: $percent.");
		}
	}
}
