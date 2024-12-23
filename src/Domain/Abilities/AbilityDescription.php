<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class AbilityDescription
{
	public function __construct(
		private(set) VersionGroupId $versionGroupId,
		private(set) LanguageId $languageId,
		private(set) AbilityId $abilityId,
		private(set) string $description,
	) {}
}
