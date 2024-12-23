<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class ItemDescription
{
	public function __construct(
		private(set) VersionGroupId $versionGroupId,
		private(set) LanguageId $languageId,
		private(set) ItemId $itemId,
		private(set) string $name,
		private(set) string $description,
	) {}
}
