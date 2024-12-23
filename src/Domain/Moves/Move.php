<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

final readonly class Move
{
	public function __construct(
		private(set) MoveId $id,
		private(set) string $identifier,
		private(set) MoveType $type,
	) {}
}
