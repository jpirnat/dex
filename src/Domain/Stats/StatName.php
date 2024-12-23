<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class StatName
{
	public function __construct(
		private(set) LanguageId $languageId,
		private(set) StatId $statId,
		private(set) string $name,
		private(set) string $abbreviation,
	) {}
}
