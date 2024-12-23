<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class MoveDescription
{
	public function __construct(
		private(set) VersionGroupId $versionGroupId,
		private(set) LanguageId $languageId,
		private(set) MoveId $moveId,
		private(set) string $description,
	) {}
}
