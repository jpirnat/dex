<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Characteristics;

use Jp\Dex\Domain\Stats\StatId;

final readonly class Characteristic
{
	public function __construct(
		private(set) CharacteristicId $id,
		private(set) string $identifier,
		private(set) StatId $highestStatId,
		private(set) int $ivModFive,
	) {}
}
