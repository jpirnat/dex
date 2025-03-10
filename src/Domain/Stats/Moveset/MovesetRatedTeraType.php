<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;
use Jp\Dex\Domain\Types\TypeId;

final readonly class MovesetRatedTeraType
{
	/**
	 * Constructor.
	 *
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		private(set) UsageRatedPokemonId $usageRatedPokemonId,
		private(set) TypeId $typeId,
		private(set) float $percent,
	) {
		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException("Invalid percent: $percent.");
		}
	}
}
