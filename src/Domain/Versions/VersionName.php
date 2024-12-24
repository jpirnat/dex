<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class VersionName
{
	public function __construct(
		private(set) LanguageId $languageId,
		private(set) VersionId $versionId,
		private(set) string $name,
	) {}
}
