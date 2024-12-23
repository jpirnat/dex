<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

final readonly class Stat
{
	public function __construct(
		private(set) StatId $id,
		private(set) string $identifier,
	) {}
}
