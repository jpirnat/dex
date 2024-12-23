<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class AbilityName
{
	public function __construct(
		private(set) LanguageId $languageId,
		private(set) AbilityId $abilityId,
		private(set) string $name,
	) {}
}
