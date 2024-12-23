<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Conditions;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class ConditionName
{
	public function __construct(
		private(set) LanguageId $languageId,
		private(set) ConditionId $conditionId,
		private(set) string $name,
	) {}
}
