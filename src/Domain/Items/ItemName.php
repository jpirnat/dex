<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class ItemName
{
	public function __construct(
		private(set) LanguageId $languageId,
		private(set) ItemId $itemId,
		private(set) string $name,
	) {}
}
