<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Stats\StatId;

final readonly class Nature
{
	public function __construct(
		private(set) NatureId $id,
		private(set) string $identifier,
		private(set) ?StatId $increasedStatId,
		private(set) ?StatId $decreasedStatId,
		private(set) FormId $toxelEvoId,
		private(set) int $vcExpRemainder,
	) {}
}
